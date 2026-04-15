<?php
// api/attendees.php
// Admin sees all bookings; organiser sees only bookings for their own events
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $role   = strtolower($_SESSION['role']   ?? 'requester');
    $userID = (int)($_SESSION['user_id']     ?? 0);

    $search  = trim($_GET['search']  ?? '');
    $eventId = trim($_GET['eventId'] ?? '');
    $status  = trim($_GET['status']  ?? '');

    $params = [];
    $where  = [];

    // Organiser: restrict to their own events only
    if ($role === 'organiser') {
        $where[]  = "e.OrganizerID = ?";
        $params[] = $userID;
    }

    if (!empty($search)) {
        $where[]  = "(u.Name LIKE ? OR u.Email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($eventId)) {
        $where[]  = "b.EventID = ?";
        $params[] = $eventId;
    }

    if (!empty($status)) {
        $where[]  = "b.Status = ?";
        $params[] = strtoupper($status);
    }

    // Always exclude CANCELLED by default unless the user explicitly filters for it
    if (empty($status)) {
        $where[]  = "b.Status != 'CANCELLED'";
    }

    $whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->prepare("
        SELECT
            b.BookingID,
            b.Status        AS BookingStatus,
            b.RequestedAt,
            b.DepositAmount,
            b.Notes,
            u.Name          AS AttendeeName,
            u.Email         AS AttendeeEmail,
            e.EventID,
            e.Title         AS EventName,
            e.CapacityLimit,
            v.Name          AS VenueName,
            t.EventDate,
            CONCAT(COALESCE(t.StartTime,''), '–', COALESCE(t.EndTime,'')) AS TimeSlot,
            (SELECT COUNT(*) FROM booking b2
             WHERE b2.EventID = e.EventID
               AND b2.Status IN ('PENDING','APPROVED')) AS TotalRegistered
        FROM booking b
        JOIN user u  ON b.UserID  = u.UserID
        JOIN event e ON b.EventID = e.EventID
        LEFT JOIN venue v           ON b.VenueID  = v.VenueID
        LEFT JOIN event_schedule es ON e.EventID  = es.EventID
        LEFT JOIN timeslot t        ON es.SlotID   = t.SlotID
        $whereSQL
        ORDER BY e.Title ASC, b.Status ASC, u.Name ASC
    ");
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Events dropdown — organiser sees only their events
    if ($role === 'organiser') {
        $evStmt = $pdo->prepare("
            SELECT EventID, Title FROM event
            WHERE OrganizerID = ? AND Status != 'CANCELLED'
            ORDER BY Title ASC
        ");
        $evStmt->execute([$userID]);
        $events = $evStmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $events = $pdo->query("
            SELECT EventID, Title FROM event
            WHERE Status != 'CANCELLED'
            ORDER BY Title ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success'  => true,
        'bookings' => $bookings,
        'events'   => $events,
        'role'     => $role
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
