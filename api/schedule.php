<?php
// api/schedule.php - Search available slots by venue and date
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $venueId   = $_GET['venueId']   ?? '';
    $eventDate = $_GET['eventDate'] ?? '';

    if (empty($eventDate)) {
        echo json_encode(['success' => false, 'error' => 'Date is required']);
        exit;
    }

    // Get all venues for dropdown if no venue filter
    $venues = $pdo->query("
        SELECT VenueID, Name AS VenueName, MaxCapacity
        FROM venue
        WHERE IsActive = TRUE
        ORDER BY Name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Get booked slots for this date (and optional venue)
    $params = [$eventDate];
    $venueFilter = '';
    if (!empty($venueId)) {
        $venueFilter = 'AND es.VenueID = ?';
        $params[] = $venueId;
    }

    $bookedSlots = $pdo->prepare("
        SELECT 
            t.SlotID,
            t.StartTime,
            t.EndTime,
            v.VenueID,
            v.Name AS VenueName,
            e.Title AS EventName,
            e.CapacityLimit,
            COUNT(b.BookingID) AS Registered
        FROM timeslot t
        JOIN event_schedule es ON t.SlotID = es.SlotID
        JOIN venue v ON es.VenueID = v.VenueID
        JOIN event e ON es.EventID = e.EventID
        LEFT JOIN booking b ON e.EventID = b.EventID
        WHERE t.EventDate = ?
          AND e.Status != 'CANCELLED'
          $venueFilter
        GROUP BY t.SlotID, t.StartTime, t.EndTime, v.VenueID, v.Name, e.Title, e.CapacityLimit
        ORDER BY t.StartTime ASC
    ");
    $bookedSlots->execute($params);
    $booked = $bookedSlots->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'date'    => $eventDate,
        'venues'  => $venues,
        'booked'  => $booked
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
