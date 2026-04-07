<?php
// api/today-key-items.php
// Now shows ALL upcoming events (not just today)

header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $today = date('Y-m-d');

    $stmt = $pdo->prepare("
        SELECT 
            e.EventID,
            e.EventName,
            e.EventDate,
            CONCAT(e.StartTime, '–', e.EndTime) as TimeSlot,
            v.VenueName,
            e.Capacity,
            COUNT(b.BookingID) as Registered,
            CASE 
                WHEN COUNT(b.BookingID) >= e.Capacity * 0.95 THEN 'danger'
                WHEN COUNT(b.BookingID) >= e.Capacity * 0.80 THEN 'warn'
                ELSE 'ok'
            END as Status
        FROM event e
        LEFT JOIN venue v ON e.VenueID = v.VenueID
        LEFT JOIN booking b ON e.EventID = b.EventID
        WHERE e.EventDate >= ?          -- Show upcoming events only
        GROUP BY e.EventID, e.EventName, e.EventDate, e.StartTime, e.EndTime, v.VenueName, e.Capacity
        ORDER BY e.EventDate ASC, e.StartTime ASC
        LIMIT 8
    ");

    $stmt->execute([$today]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the date nicely for display
    foreach ($items as &$item) {
        $date = new DateTime($item['EventDate']);
        $item['EventDateFormatted'] = $date->format('M j, Y');
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'count' => count($items)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
