<?php
// api/save-event.php - Create or update event with ownership enforcement
//
// TRIGGER INTEGRATION:
//   Trigger 3 (trg_venue_deactivated) — venue deactivation auto-cancels confirmed events.
//             We now verify the selected venue is still ACTIVE before saving any event.
//             This prevents creating a new event at a venue that was just deactivated,
//             and gives a clear error message instead of a silent failure.

header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $input       = json_decode(file_get_contents('php://input'), true);
    $title        = trim($input['title']        ?? '');
    $description  = trim($input['description']  ?? '');
    $capacity     = (int)($input['capacity']    ?? 0);
    $venueId      = (int)($input['venueId']     ?? 0);
    $eventDate    = trim($input['eventDate']    ?? '');
    $startTime    = trim($input['startTime']    ?? '');
    $endTime      = trim($input['endTime']      ?? '');
    $editingID    = (int)($input['eventID']     ?? 0);
    $organizerID  = (int)($_SESSION['user_id']  ?? 0);
    $role         = strtolower($_SESSION['role'] ?? 'requester');
    $isPrivate    = !empty($input['isPrivate']) ? 1 : 0;
    $inviteEmails = isset($input['inviteEmails']) && is_array($input['inviteEmails'])
        ? $input['inviteEmails'] : [];

    // Only admin and organiser can save events
    if ($role === 'requester') {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to create or edit events.']);
        exit;
    }

    // Validate required fields
    if (empty($title) || $venueId <= 0 || empty($eventDate) || empty($startTime) || empty($endTime) || $capacity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
        exit;
    }

    if ($capacity > 300) {
        echo json_encode(['success' => false, 'error' => 'Capacity cannot exceed 300 guests.']);
        exit;
    }

    if ($startTime >= $endTime) {
        echo json_encode(['success' => false, 'error' => 'End time must be after start time.']);
        exit;
    }

    // ── Trigger 3 guard: verify venue is still active ─────────────────────
    // Trigger 3 can deactivate a venue at any time — we must confirm it's still usable
    // before letting anyone create or update an event there.
    $venueCheck = $pdo->prepare("
        SELECT Name, MaxCapacity, IsActive FROM venue WHERE VenueID = ?
    ");
    $venueCheck->execute([$venueId]);
    $venue = $venueCheck->fetch(PDO::FETCH_ASSOC);

    if (!$venue) {
        echo json_encode(['success' => false, 'error' => 'Selected venue does not exist.']);
        exit;
    }

    if (!(bool)$venue['IsActive']) {
        echo json_encode([
            'success' => false,
            'error'   => "Sorry, \"{$venue['Name']}\" is no longer available. " .
                "It has been deactivated and all events there have been cancelled. " .
                "Please select a different venue."
        ]);
        exit;
    }

    // Warn if event capacity exceeds venue's maximum
    if ($capacity > (int)$venue['MaxCapacity']) {
        echo json_encode([
            'success' => false,
            'error'   => "Capacity ($capacity) exceeds {$venue['Name']}'s maximum of {$venue['MaxCapacity']} guests."
        ]);
        exit;
    }

    // ── Ownership check for organiser edits ───────────────────────────────
    if ($editingID > 0 && $role === 'organiser') {
        $ownerCheck = $pdo->prepare("SELECT OrganizerID FROM event WHERE EventID = ?");
        $ownerCheck->execute([$editingID]);
        $existing = $ownerCheck->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            echo json_encode(['success' => false, 'error' => 'Event not found.']);
            exit;
        }

        if ((int)$existing['OrganizerID'] !== $organizerID) {
            echo json_encode(['success' => false, 'error' => 'You can only edit events you created.']);
            exit;
        }
    }

    $pdo->beginTransaction();

    // ── Conflict check: same venue, overlapping time, different event ─────
    $conflictStmt = $pdo->prepare("
        SELECT e.Title FROM event e
        JOIN event_schedule es ON e.EventID = es.EventID
        JOIN timeslot t        ON es.SlotID  = t.SlotID
        WHERE es.VenueID  = ?
          AND t.EventDate  = ?
          AND t.StartTime  < ?
          AND t.EndTime    > ?
          AND e.EventID   != ?
          AND e.Status    != 'CANCELLED'
        LIMIT 1
    ");
    $conflictStmt->execute([$venueId, $eventDate, $endTime, $startTime, $editingID]);
    $clash = $conflictStmt->fetch(PDO::FETCH_ASSOC);

    if ($clash) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'error'   => "Venue conflict: \"{$clash['Title']}\" is already booked at {$venue['Name']} during that time."
        ]);
        exit;
    }

    // ── Create or reuse timeslot ──────────────────────────────────────────
    // INSERT IGNORE prevents duplicate timeslot rows (enforced by uq_timeslot constraint)
    $slotStmt = $pdo->prepare("INSERT IGNORE INTO timeslot (EventDate, StartTime, EndTime) VALUES (?, ?, ?)");
    $slotStmt->execute([$eventDate, $startTime, $endTime]);

    // If INSERT IGNORE skipped (identical slot already existed), look it up
    $newSlotID = $pdo->lastInsertId();
    if (!$newSlotID) {
        $fetchSlot = $pdo->prepare("SELECT SlotID FROM timeslot WHERE EventDate=? AND StartTime=? AND EndTime=? LIMIT 1");
        $fetchSlot->execute([$eventDate, $startTime, $endTime]);
        $newSlotID = $fetchSlot->fetchColumn();
    }

    if ($editingID > 0 && $role === 'admin') {
        // ── Admin: update existing event ──────────────────────────────────
        $pdo->prepare("UPDATE event SET Title=?, Description=?, CapacityLimit=?, IsPrivate=? WHERE EventID=?")
            ->execute([$title, $description, $capacity, $isPrivate, $editingID]);

        $pdo->prepare("UPDATE event_schedule SET VenueID=?, SlotID=? WHERE EventID=?")
            ->execute([$venueId, $newSlotID, $editingID]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Event updated successfully!', 'eventID' => $editingID]);
    } else {
        // ── Admin or organiser: create new event ──────────────────────────
        $eventStmt = $pdo->prepare("
            INSERT INTO event (Title, Description, OrganizerID, CapacityLimit, Status, IsPrivate)
            VALUES (?, ?, ?, ?, 'CONFIRMED', ?)
        ");
        $eventStmt->execute([$title, $description, $organizerID, $capacity, $isPrivate]);
        $newEventID = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO event_schedule (VenueID, SlotID, EventID) VALUES (?, ?, ?)")
            ->execute([$venueId, $newSlotID, $newEventID]);

        // ── Handle invites for private events ─────────────────────────────
        // Each invited email gets an APPROVED booking automatically
        $notFound = [];
        if ($isPrivate && !empty($inviteEmails)) {
            // Fetch the venue for the booking record
            $venueForBooking = $venueId;

            $findUser = $pdo->prepare("SELECT UserID FROM user WHERE Email = ? LIMIT 1");
            $insertBooking = $pdo->prepare("
                INSERT IGNORE INTO booking (UserID, VenueID, EventID, Status, DepositAmount, Notes)
                VALUES (?, ?, ?, 'APPROVED', 0, 'Invited by organiser')
            ");

            foreach ($inviteEmails as $email) {
                $email = trim(strtolower($email));
                if (empty($email)) continue;

                $findUser->execute([$email]);
                $inviteeID = $findUser->fetchColumn();

                if ($inviteeID) {
                    $insertBooking->execute([$inviteeID, $venueForBooking, $newEventID]);
                } else {
                    $notFound[] = $email; // email not registered in the system
                }
            }
        }

        $pdo->commit();

        $message = $isPrivate ? 'Private event created successfully!' : 'Event created successfully!';
        $response = ['success' => true, 'message' => $message, 'eventID' => $newEventID];
        if (!empty($notFound)) {
            $response['notFound'] = $notFound;
            $response['warning'] = 'Some emails were not found in the system: ' . implode(', ', $notFound);
        }
        echo json_encode($response);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
