<?php
// api/events.php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // Join event with venue to get meaningful data
    $stmt = $pdo->prepare("
        SELECT 
            e.EventID,
            e.EventName,
            e.EventDate,
            e.StartTime,
            e.EndTime,
            e.Capacity,
            v.VenueName,
            v.Location,
            COUNT(b.BookingID) as Registered
        FROM event e
        LEFT JOIN venue v ON e.VenueID = v.VenueID
        LEFT JOIN booking b ON e.EventID = b.EventID
        GROUP BY e.EventID
        ORDER BY e.EventDate ASC
        LIMIT 10
    ");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'events' => $events]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
