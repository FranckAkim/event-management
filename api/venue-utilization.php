<?php
// api/venue-utilization.php
// Returns venue usage stats for the admin dashboard Venue Utilization panel
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $role = strtolower($_SESSION['role'] ?? 'requester');
    if ($role !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Admin only.']);
        exit;
    }

    $venues = $pdo->query("
        SELECT
            v.VenueID,
            v.Name            AS VenueName,
            v.MaxCapacity,
            v.IsActive,

            -- Total confirmed events at this venue (all time)
            COUNT(DISTINCT CASE WHEN e.Status = 'CONFIRMED' THEN e.EventID END)
                AS TotalEvents,

            -- Events this week (next 7 days)
            COUNT(DISTINCT CASE
                WHEN e.Status = 'CONFIRMED'
                 AND t.EventDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                THEN e.EventID END)
                AS EventsThisWeek,

            -- Total active registrations across all events at this venue
            COALESCE(SUM(CASE WHEN b.Status IN ('PENDING','APPROVED') THEN 1 ELSE 0 END), 0)
                AS TotalRegistered,

            -- Most upcoming event name
            (SELECT e2.Title FROM event e2
             JOIN event_schedule es2 ON e2.EventID = es2.EventID
             JOIN timeslot t2        ON es2.SlotID  = t2.SlotID
             WHERE es2.VenueID = v.VenueID
               AND e2.Status   = 'CONFIRMED'
               AND t2.EventDate >= CURDATE()
             ORDER BY t2.EventDate ASC, t2.StartTime ASC
             LIMIT 1) AS NextEventName,

            -- Next event date
            (SELECT t2.EventDate FROM event e2
             JOIN event_schedule es2 ON e2.EventID = es2.EventID
             JOIN timeslot t2        ON es2.SlotID  = t2.SlotID
             WHERE es2.VenueID = v.VenueID
               AND e2.Status   = 'CONFIRMED'
               AND t2.EventDate >= CURDATE()
             ORDER BY t2.EventDate ASC, t2.StartTime ASC
             LIMIT 1) AS NextEventDate

        FROM venue v
        LEFT JOIN event_schedule es ON v.VenueID  = es.VenueID
        LEFT JOIN event e           ON es.EventID  = e.EventID
        LEFT JOIN booking b         ON e.EventID   = b.EventID
        GROUP BY v.VenueID, v.Name, v.MaxCapacity, v.IsActive
        ORDER BY TotalEvents DESC, v.Name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Cast types
    foreach ($venues as &$v) {
        $v['TotalEvents']     = (int)$v['TotalEvents'];
        $v['EventsThisWeek']  = (int)$v['EventsThisWeek'];
        $v['TotalRegistered'] = (int)$v['TotalRegistered'];
        $v['MaxCapacity']     = (int)$v['MaxCapacity'];
        $v['IsActive']        = (bool)$v['IsActive'];
    }
    unset($v);

    // Overall stats
    $totalActiveVenues = count(array_filter($venues, fn($v) => $v['IsActive']));
    $busiestVenue      = $venues[0]['VenueName'] ?? '—';

    echo json_encode([
        'success'           => true,
        'venues'            => $venues,
        'totalActiveVenues' => $totalActiveVenues,
        'busiestVenue'      => $busiestVenue
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
