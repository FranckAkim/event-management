<?php
// api/remove-guest.php
// Sets a booking to CANCELLED in the database
// Admin: can remove any guest from any event
// Organiser: can only remove guests from their own events
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $input     = json_decode(file_get_contents('php://input'), true);
    $bookingID = (int)($input['bookingID'] ?? 0);
    $userID    = (int)($_SESSION['user_id'] ?? 0);
    $role      = strtolower($_SESSION['role'] ?? 'requester');

    // Permission check
    if ($role === 'requester') {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to remove guests.']);
        exit;
    }

    if ($bookingID <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid booking ID.']);
        exit;
    }

    // Fetch booking + event + guest info
    $check = $pdo->prepare("
        SELECT
            b.BookingID,
            b.Status,
            b.EventID,
            e.OrganizerID,
            e.Title  AS EventTitle,
            u.Name   AS GuestName,
            u.Email  AS GuestEmail
        FROM booking b
        JOIN event e ON b.EventID = e.EventID
        JOIN user  u ON b.UserID  = u.UserID
        WHERE b.BookingID = ?
        LIMIT 1
    ");
    $check->execute([$bookingID]);
    $booking = $check->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode(['success' => false, 'error' => 'Booking not found.']);
        exit;
    }

    // Organiser ownership check
    if ($role === 'organiser' && (int)$booking['OrganizerID'] !== $userID) {
        echo json_encode(['success' => false, 'error' => 'You can only remove guests from your own events.']);
        exit;
    }

    // Already cancelled — nothing to do
    if (strtoupper($booking['Status']) === 'CANCELLED') {
        echo json_encode(['success' => false, 'error' => 'This guest is already cancelled.']);
        exit;
    }

    // ── Perform update inside a transaction ───────────────────────
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        UPDATE booking
        SET Status = 'CANCELLED'
        WHERE BookingID = ?
          AND Status != 'CANCELLED'
    ");
    $stmt->execute([$bookingID]);

    // Confirm the row was actually updated
    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Booking could not be updated. It may have already been cancelled.']);
        exit;
    }

    // Fetch fresh active count for this event after the update
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) AS Active
        FROM booking
        WHERE EventID = ?
          AND Status IN ('PENDING', 'APPROVED')
    ");
    $countStmt->execute([$booking['EventID']]);
    $active = (int)$countStmt->fetchColumn();

    $pdo->commit();

    echo json_encode([
        'success'     => true,
        'message'     => "{$booking['GuestName']} has been removed from \"{$booking['EventTitle']}\".",
        'bookingID'   => (int)$bookingID,
        'eventID'     => (int)$booking['EventID'],
        'activeCount' => $active
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
