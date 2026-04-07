<?php
// api/save-event.php - Create or update event with ownership enforcement
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

try {
    $input       = json_decode(file_get_contents('php://input'), true);
    $title       = trim($input['title']       ?? '');
    $description = trim($input['description'] ?? '');
    $capacity    = (int)($input['capacity']   ?? 0);
    $venueId     = (int)($input['venueId']    ?? 0);
    $eventDate   = trim($input['eventDate']   ?? '');
    $startTime   = trim($input['startTime']   ?? '');
    $endTime     = trim($input['endTime']     ?? '');
    $editingID   = (int)($input['eventID']    ?? 0);
    $organizerID = (int)($_SESSION['user_id'] ?? 0);
    $role        = strtolower($_SESSION['role'] ?? 'requester');

    // Only admin and organiser can save events
    if ($role === 'requester') {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to create or edit events.']);
        exit;
    }

    // Validate fields
    if (empty($title) || $venueId <= 0 || empty($eventDate) || empty($startTime) || empty($endTime) || $capacity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    if ($capacity > 300) {
        echo json_encode(['success' => false, 'error' => 'Capacity cannot exceed 300 guests.']);
        exit;
    }

    if ($startTime >= $endTime) {
        echo json_encode(['success' => false, 'error' => 'End time must be after start time']);
        exit;
    }

    // ── Ownership check for edits ──────────────────────────────────────────
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

    // Check for venue + time overlap (exclude the event being edited)
    $conflictStmt = $pdo->prepare("
        SELECT e.Title FROM event e
        JOIN event_schedule es ON e.EventID = es.EventID
        JOIN timeslot t        ON es.SlotID  = t.SlotID
        WHERE es.VenueID  = ?
          AND t.EventDate  = ?
          AND t.StartTime  < ?
          AND t.EndTime    > ?
          AND e.EventID   != ?
        LIMIT 1
    ");
    $conflictStmt->execute([$venueId, $eventDate, $endTime, $startTime, $editingID]);
    $clash = $conflictStmt->fetch(PDO::FETCH_ASSOC);

    if ($clash) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'error'   => "Venue conflict: \"{$clash['Title']}\" is already booked at this venue during that time."
        ]);
        exit;
    }

    // Create a new timeslot
    $slotStmt = $pdo->prepare("INSERT INTO timeslot (EventDate, StartTime, EndTime) VALUES (?, ?, ?)");
    $slotStmt->execute([$eventDate, $startTime, $endTime]);
    $newSlotID = $pdo->lastInsertId();

    if ($editingID > 0 && $role === 'admin') {
        // Admin editing: update the event row, replace timeslot
        $pdo->prepare("UPDATE event SET Title=?, Description=?, CapacityLimit=? WHERE EventID=?")
            ->execute([$title, $description, $capacity, $editingID]);

        // Update event_schedule slot
        $pdo->prepare("UPDATE event_schedule SET VenueID=?, SlotID=? WHERE EventID=?")
            ->execute([$venueId, $newSlotID, $editingID]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Event updated successfully!', 'eventID' => $editingID]);
    } else {
        // Create new event (always creates — admin and organiser)
        $eventStmt = $pdo->prepare("
            INSERT INTO event (Title, Description, OrganizerID, CapacityLimit, Status)
            VALUES (?, ?, ?, ?, 'CONFIRMED')
        ");
        $eventStmt->execute([$title, $description, $organizerID, $capacity]);
        $newEventID = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO event_schedule (VenueID, SlotID, EventID) VALUES (?, ?, ?)")
            ->execute([$venueId, $newSlotID, $newEventID]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Event created successfully!', 'eventID' => $newEventID]);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
