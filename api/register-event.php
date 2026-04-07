<?php
// api/register-event.php
// booking schema: BookingID, UserID, VenueID, EventID, Status, RequestedAt, DepositAmount, Notes
// Status constraint: 'PENDING','APPROVED','REJECTED','CANCELLED'
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

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
    $eventStmt = $pdo->prepare("
        SELECT
            e.EventID, e.Title, e.CapacityLimit, e.Status,
            COUNT(b.BookingID) AS Registered,
            es.VenueID
        FROM event e
        LEFT JOIN booking b         ON e.EventID = b.EventID
        LEFT JOIN event_schedule es ON e.EventID = es.EventID
        WHERE e.EventID = ?
        GROUP BY e.EventID, e.Title, e.CapacityLimit, e.Status, es.VenueID
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

    // Check capacity
    if ((int)$event['Registered'] >= (int)$event['CapacityLimit']) {
        echo json_encode(['success' => false, 'error' => "Sorry, \"{$event['Title']}\" is fully booked."]);
        exit;
    }

    // ── INSERT new booking ─────────────────────────────────────────────────
    // Status starts as 'PENDING' — admin/organiser approves
    $pdo->prepare("
        INSERT INTO booking (UserID, VenueID, EventID, Status, RequestedAt, DepositAmount, Notes)
        VALUES (?, ?, ?, 'PENDING', NOW(), 0, '')
    ")->execute([$userID, $venueID, $eventID]);

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
