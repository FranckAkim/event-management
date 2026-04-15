<?php
// api/update-booking.php
// Approve or reject a booking — admin or organiser only
// Returns updated capacity counts so the frontend can refresh without a full reload
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $input     = json_decode(file_get_contents('php://input'), true);
    $bookingID = (int)($input['bookingID'] ?? 0);
    $newStatus = strtoupper(trim($input['status'] ?? ''));
    $userID    = (int)($_SESSION['user_id'] ?? 0);
    $role      = strtolower($_SESSION['role'] ?? 'requester');

    // Permission check
    if ($role === 'requester') {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to update bookings.']);
        exit;
    }

    if (!in_array($newStatus, ['APPROVED', 'REJECTED'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid status. Must be APPROVED or REJECTED.']);
        exit;
    }

    if ($bookingID <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid booking ID.']);
        exit;
    }

    // Fetch booking + event info for ownership check and return data
    $check = $pdo->prepare("
        SELECT
            b.BookingID,
            b.Status       AS OldStatus,
            b.EventID,
            e.OrganizerID,
            e.Title        AS EventTitle,
            e.CapacityLimit
        FROM booking b
        JOIN event e ON b.EventID = e.EventID
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
        echo json_encode(['success' => false, 'error' => 'You can only manage bookings for your own events.']);
        exit;
    }

    // Prevent actioning an already-processed booking
    if (in_array(strtoupper($booking['OldStatus']), ['APPROVED', 'REJECTED', 'CANCELLED'])) {
        echo json_encode([
            'success' => false,
            'error'   => "This booking is already {$booking['OldStatus']} and cannot be changed."
        ]);
        exit;
    }

    // ── Perform the update inside a transaction ───────────────────────────
    $pdo->beginTransaction();

    $pdo->prepare("UPDATE booking SET Status = ? WHERE BookingID = ?")
        ->execute([$newStatus, $bookingID]);

    // ── Return fresh counts for this event so frontend stays in sync ──────
    $freshCount = $pdo->prepare("
        SELECT
            COUNT(*) AS Total,
            SUM(CASE WHEN Status = 'APPROVED' THEN 1 ELSE 0 END) AS Approved,
            SUM(CASE WHEN Status = 'PENDING'  THEN 1 ELSE 0 END) AS Pending,
            SUM(CASE WHEN Status IN ('PENDING','APPROVED') THEN 1 ELSE 0 END) AS Active
        FROM booking
        WHERE EventID = ?
    ");
    $freshCount->execute([$booking['EventID']]);
    $counts = $freshCount->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    $label = $newStatus === 'APPROVED' ? 'approved' : 'declined';

    echo json_encode([
        'success'   => true,
        'message'   => "Booking {$label} for \"{$booking['EventTitle']}\".",
        'status'    => $newStatus,
        'eventID'   => (int)$booking['EventID'],
        'capacity'  => (int)$booking['CapacityLimit'],
        'counts'    => [
            'total'    => (int)$counts['Total'],
            'approved' => (int)$counts['Approved'],
            'pending'  => (int)$counts['Pending'],
            'active'   => (int)$counts['Active']
        ]
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
