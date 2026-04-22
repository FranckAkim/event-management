<?php
// api/register-event.php
// booking schema: BookingID, UserID, VenueID, EventID, Status, RequestedAt, DepositAmount, Notes
// Status constraint: 'PENDING','APPROVED','REJECTED','CANCELLED'
//
// TRIGGER INTEGRATION:
//   Trigger 1 (trg_booking_before_insert)  — DB auto-sets RequestedAt, so we no longer pass NOW()
//   Trigger 2 (trg_booking_check_capacity) — DB enforces capacity limit; we catch the SQLSTATE 45000
//                                             error and return a friendly message instead of crashing

header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $input   = json_decode(file_get_contents('php://input'), true);
    $eventID = (int)($input['eventID'] ?? 0);
    $action  = trim($input['action']   ?? 'register');
    $userID  = (int)($_SESSION['user_id'] ?? 0);

    if ($eventID <= 0 || $userID <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
    }

    // ── Fetch event + venue ───────────────────────────────────────────────
    // Count only PENDING and APPROVED bookings — matches Trigger 2's logic exactly
    $eventStmt = $pdo->prepare("
        SELECT
            e.EventID,
            e.Title,
            e.CapacityLimit,
            e.Status,
            es.VenueID,
            (SELECT COUNT(*)
             FROM booking bx
             WHERE bx.EventID = e.EventID
               AND bx.Status IN ('PENDING','APPROVED')
            ) AS Registered
        FROM event e
        LEFT JOIN event_schedule es ON e.EventID = es.EventID
        WHERE e.EventID = ?
        LIMIT 1
    ");
    $eventStmt->execute([$eventID]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo json_encode(['success' => false, 'error' => 'Event not found.']);
        exit;
    }

    if (strtoupper($event['Status']) !== 'CONFIRMED') {
        echo json_encode(['success' => false, 'error' => 'This event is not available for registration.']);
        exit;
    }

    $venueID = (int)($event['VenueID'] ?? 0);

    // ── Check if already registered ───────────────────────────────────────
    $existingStmt = $pdo->prepare("
        SELECT BookingID, Status FROM booking
        WHERE EventID = ? AND UserID = ?
        LIMIT 1
    ");
    $existingStmt->execute([$eventID, $userID]);
    $booking = $existingStmt->fetch(PDO::FETCH_ASSOC);

    // ── Cancel registration ───────────────────────────────────────────────
    if ($action === 'cancel') {
        if (!$booking) {
            echo json_encode(['success' => false, 'error' => 'You are not registered for this event.']);
            exit;
        }
        $pdo->prepare("DELETE FROM booking WHERE BookingID = ?")
            ->execute([$booking['BookingID']]);
        echo json_encode([
            'success' => true,
            'message' => 'Registration cancelled successfully.',
            'action'  => 'cancelled'
        ]);
        exit;
    }

    // ── Register ──────────────────────────────────────────────────────────
    if ($booking) {
        $currentStatus = strtoupper($booking['Status'] ?? '');
        if ($currentStatus === 'APPROVED') {
            echo json_encode(['success' => false, 'error' => 'You are already registered for this event.']);
        } elseif ($currentStatus === 'PENDING') {
            echo json_encode(['success' => false, 'error' => 'Your registration is already pending approval.']);
        } else {
            // Re-register from CANCELLED or REJECTED state
            $pdo->prepare("UPDATE booking SET Status = 'PENDING' WHERE BookingID = ?")
                ->execute([$booking['BookingID']]);
            echo json_encode([
                'success' => true,
                'message' => 'Registration re-submitted and pending approval!',
                'action'  => 'registered'
            ]);
        }
        exit;
    }

    // ── PHP-level capacity pre-check (fast fail before hitting the DB trigger) ──
    // Trigger 2 enforces this at DB level too — this just gives a nicer early message
    if ((int)$event['Registered'] >= (int)$event['CapacityLimit']) {
        echo json_encode([
            'success' => false,
            'error'   => "Sorry, \"{$event['Title']}\" is fully booked."
        ]);
        exit;
    }

    // ── INSERT new booking ────────────────────────────────────────────────
    // NOTE: RequestedAt is intentionally omitted — Trigger 1 sets it automatically in the DB.
    // NOTE: If Trigger 2 fires (capacity exceeded), it raises SQLSTATE 45000 — caught below.
    try {
        $pdo->prepare("
            INSERT INTO booking (UserID, VenueID, EventID, Status, DepositAmount, Notes)
            VALUES (?, ?, ?, 'PENDING', 0, '')
        ")->execute([$userID, $venueID, $eventID]);
    } catch (PDOException $triggerError) {
        // Trigger 2 raises SQLSTATE 45000 with our custom message
        if ($triggerError->getCode() === '45000') {
            echo json_encode([
                'success' => false,
                'error'   => "Sorry, \"{$event['Title']}\" is fully booked. " .
                    "The capacity limit was reached just now."
            ]);
        } else {
            // Some other DB error — re-throw so the outer catch handles it
            throw $triggerError;
        }
        exit;
    }

    $spotsLeft = (int)$event['CapacityLimit'] - (int)$event['Registered'] - 1;
    $msg = "Registration submitted for \"{$event['Title']}\"! Awaiting approval.";
    if ($spotsLeft <= 10 && $spotsLeft >= 0) {
        $msg .= " Only $spotsLeft spot" . ($spotsLeft !== 1 ? "s" : "") . " left.";
    }

    echo json_encode([
        'success'   => true,
        'message'   => $msg,
        'action'    => 'registered',
        'spotsLeft' => $spotsLeft
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
