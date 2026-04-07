<?php
// api/events-list.php - Role-based event filtering with registration status
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

try {
    $role   = strtolower($_SESSION['role']   ?? 'requester');
    $userID = (int)($_SESSION['user_id']     ?? 0);

    $params = [];
    $where  = "";

    if ($role === 'organiser') {
        $where    = "WHERE e.OrganizerID = ?";
        $params[] = $userID;
    } elseif ($role === 'requester') {
        $where = "WHERE e.Status = 'CONFIRMED'";
    }
    // admin: no filter

    // For requesters, also check if they are registered for each event
    $registrationSubquery = $role === 'requester'
        ? ",(SELECT COUNT(*) FROM booking bx WHERE bx.EventID = e.EventID AND bx.UserID = $userID AND bx.Status IN ('PENDING','APPROVED')) AS IsRegistered, (SELECT bx2.Status FROM booking bx2 WHERE bx2.EventID = e.EventID AND bx2.UserID = $userID LIMIT 1) AS RegistrationStatus"
        : ",0 AS IsRegistered, NULL AS RegistrationStatus";

    $stmt = $pdo->prepare("
        SELECT
            e.EventID,
            e.Title         AS EventName,
            e.Description,
            e.CapacityLimit AS Capacity,
            e.Status,
            e.OrganizerID,
            u.Name          AS Organizer,
            v.Name          AS VenueName,
            v.VenueID,
            t.SlotID,
            t.EventDate,
            CONCAT(COALESCE(t.StartTime, ''), '–', COALESCE(t.EndTime, '')) AS TimeSlot,
            COUNT(b.BookingID) AS Registered,
            CASE
                WHEN COUNT(b.BookingID) >= e.CapacityLimit * 0.95 THEN 'danger'
                WHEN COUNT(b.BookingID) >= e.CapacityLimit * 0.85 THEN 'warn'
                ELSE 'ok'
            END AS CapacityStatus
            $registrationSubquery
        FROM event e
        LEFT JOIN user u            ON e.OrganizerID = u.UserID
        LEFT JOIN event_schedule es ON e.EventID     = es.EventID
        LEFT JOIN venue v           ON es.VenueID    = v.VenueID
        LEFT JOIN timeslot t        ON es.SlotID     = t.SlotID
        LEFT JOIN booking b         ON e.EventID     = b.EventID
        $where
        GROUP BY
            e.EventID, e.Title, e.Description, e.CapacityLimit, e.Status, e.OrganizerID,
            u.Name, v.VenueID, v.Name, t.SlotID, t.EventDate, t.StartTime, t.EndTime
        ORDER BY t.EventDate ASC, t.StartTime ASC
    ");

    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cast IsRegistered to bool
    foreach ($events as &$ev) {
        $ev['IsRegistered'] = (bool)($ev['IsRegistered'] ?? false);
    }

    echo json_encode([
        'success' => true,
        'events'  => $events,
        'role'    => $role,
        'userID'  => $userID
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
