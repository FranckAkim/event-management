<?php
// api/export.php - CSV export of events with filters
require_once '../config/db.php';

$startDate = $_GET['startDate'] ?? date('Y-m-01');
$endDate   = $_GET['endDate']   ?? date('Y-m-t');
$venueId   = $_GET['venueId']   ?? '';

$params      = [$startDate, $endDate];
$venueFilter = '';
if (!empty($venueId)) {
    $venueFilter = 'AND es.VenueID = ?';
    $params[]    = $venueId;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            e.EventID,
            e.Title         AS EventName,
            e.Description,
            e.Status,
            e.CapacityLimit AS AttendeeLimit,
            u.Name          AS Organizer,
            v.Name          AS Venue,
            t.EventDate,
            t.StartTime,
            t.EndTime,
            COUNT(b.BookingID) AS Registered
        FROM event e
        LEFT JOIN user u            ON e.OrganizerID = u.UserID
        LEFT JOIN event_schedule es ON e.EventID     = es.EventID
        LEFT JOIN venue v           ON es.VenueID    = v.VenueID
        LEFT JOIN timeslot t        ON es.SlotID     = t.SlotID
        LEFT JOIN booking b         ON e.EventID     = b.EventID
        WHERE t.EventDate BETWEEN ? AND ?
          AND e.Status != 'CANCELLED'
          $venueFilter
        GROUP BY e.EventID, e.Title, e.Description, e.Status, e.CapacityLimit,
                 u.Name, v.Name, t.EventDate, t.StartTime, t.EndTime
        ORDER BY t.EventDate ASC, t.StartTime ASC
    ");
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="events_' . $startDate . '_to_' . $endDate . '.csv"');

    $output = fopen('php://output', 'w');

    // Header row
    fputcsv($output, [
        'Event ID',
        'Event Name',
        'Description',
        'Status',
        'Attendee Limit',
        'Registered',
        'Organizer',
        'Venue',
        'Date',
        'Start Time',
        'End Time'
    ]);

    foreach ($events as $ev) {
        fputcsv($output, [
            $ev['EventID'],
            $ev['EventName'],
            $ev['Description'],
            $ev['Status'],
            $ev['AttendeeLimit'],
            $ev['Registered'],
            $ev['Organizer'],
            $ev['Venue'],
            $ev['EventDate'],
            $ev['StartTime'],
            $ev['EndTime']
        ]);
    }

    fclose($output);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
