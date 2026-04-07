<?php
// api/week-schedule.php - Real Week Schedule from database
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $startDate = date('Y-m-d');
    $endDate   = date('Y-m-d', strtotime('+7 days'));

    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(e.EventDate, '%a') as day,
            e.EventName,
            v.VenueName,
            CONCAT(e.StartTime, '–', e.EndTime) as TimeSlot,
            CASE 
                WHEN COUNT(b.BookingID) >= e.Capacity * 0.9 THEN 'danger'
                WHEN COUNT(b.BookingID) >= e.Capacity * 0.7 THEN 'warn'
                ELSE 'ok'
            END as status
        FROM event e
        LEFT JOIN venue v ON e.VenueID = v.VenueID
        LEFT JOIN booking b ON e.EventID = b.EventID
        WHERE e.EventDate BETWEEN ? AND ?
        GROUP BY e.EventID, e.EventDate, e.EventName, v.VenueName, e.StartTime, e.EndTime
        ORDER BY e.EventDate ASC, e.StartTime ASC
    ");

    $stmt->execute([$startDate, $endDate]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group events by day
    $days = [];
    $currentDay = "";
    $dayEvents = [];

    foreach ($events as $ev) {
        if ($ev['day'] !== $currentDay) {
            if ($currentDay !== "") {
                $days[] = ["day" => $currentDay, "events" => $dayEvents];
            }
            $currentDay = $ev['day'];
            $dayEvents = [];
        }
        $dayEvents[] = $ev;
    }
    if ($currentDay !== "") {
        $days[] = ["day" => $currentDay, "events" => $dayEvents];
    }

    // Fill in missing days with empty events
    $allDays = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    $result = [];

    foreach ($allDays as $d) {
        $found = null;
        foreach ($days as $day) {
            if ($day['day'] === $d) {
                $found = $day;
                break;
            }
        }
        $result[] = [
            "day" => $d,
            "events" => $found ? $found['events'] : []
        ];
    }

    echo json_encode(['success' => true, 'days' => $result]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
