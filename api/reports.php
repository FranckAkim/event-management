<?php
// api/reports.php - Filtered report with venue/date GROUP BY queries
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $startDate = $_GET['startDate'] ?? date('Y-m-01');
    $endDate   = $_GET['endDate']   ?? date('Y-m-t');
    $venueId   = $_GET['venueId']   ?? '';

    $params      = [$startDate, $endDate];
    $venueFilter = '';
    if (!empty($venueId)) {
        $venueFilter = 'AND es.VenueID = ?';
        $params[]    = $venueId;
    }

    // Summary stats
    $summary = $pdo->prepare("
        SELECT
            COUNT(DISTINCT e.EventID)                                          AS TotalEvents,
            COUNT(DISTINCT CASE WHEN e.Status = 'CONFIRMED' THEN e.EventID END) AS ConfirmedEvents,
            COUNT(DISTINCT CASE WHEN e.Status = 'DRAFT'     THEN e.EventID END) AS DraftEvents,
            COUNT(b.BookingID)                                                 AS TotalBookings,
            COALESCE(SUM(e.CapacityLimit), 0)                                  AS TotalCapacity
        FROM event e
        LEFT JOIN event_schedule es ON e.EventID = es.EventID
        LEFT JOIN timeslot t        ON es.SlotID  = t.SlotID
        LEFT JOIN booking b         ON e.EventID  = b.EventID
        WHERE t.EventDate BETWEEN ? AND ?
          AND e.Status != 'CANCELLED'
          $venueFilter
    ");
    $summary->execute($params);
    $summaryData = $summary->fetch(PDO::FETCH_ASSOC);

    // Venue utilization
    $venueParams = [$startDate, $endDate];
    $venueUtilFilter = '';
    if (!empty($venueId)) {
        $venueUtilFilter = 'AND v.VenueID = ?';
        $venueParams[]   = $venueId;
    }
    $venueUtil = $pdo->prepare("
        SELECT
            v.Name          AS VenueName,
            COUNT(DISTINCT e.EventID) AS BookedSlots,
            SUM(e.CapacityLimit)      AS TotalCapacityUsed,
            COUNT(b.BookingID)        AS TotalRegistered,
            CASE
                WHEN COUNT(DISTINCT e.EventID) >= 5 THEN 'danger'
                WHEN COUNT(DISTINCT e.EventID) >= 3 THEN 'warn'
                ELSE 'ok'
            END AS UtilStatus
        FROM venue v
        LEFT JOIN event_schedule es ON v.VenueID  = es.VenueID
        LEFT JOIN event e           ON es.EventID = e.EventID
        LEFT JOIN timeslot t        ON es.SlotID  = t.SlotID
        LEFT JOIN booking b         ON e.EventID  = b.EventID
        WHERE (t.EventDate BETWEEN ? AND ? OR t.EventDate IS NULL)
          AND (e.Status != 'CANCELLED' OR e.Status IS NULL)
          $venueUtilFilter
        GROUP BY v.VenueID, v.Name
        ORDER BY BookedSlots DESC
    ");
    $venueUtil->execute($venueParams);
    $venueData = $venueUtil->fetchAll(PDO::FETCH_ASSOC);

    // Capacity analysis per event
    $capParams = [$startDate, $endDate];
    if (!empty($venueId)) $capParams[] = $venueId;
    $capAnalysis = $pdo->prepare("
        SELECT
            e.Title        AS EventName,
            e.CapacityLimit,
            COUNT(b.BookingID) AS Registered,
            ROUND(COUNT(b.BookingID) / e.CapacityLimit * 100, 1) AS FillPct,
            v.Name         AS VenueName,
            t.EventDate,
            CASE
                WHEN COUNT(b.BookingID) >= e.CapacityLimit       THEN 'danger'
                WHEN COUNT(b.BookingID) >= e.CapacityLimit * 0.85 THEN 'warn'
                ELSE 'ok'
            END AS CapStatus
        FROM event e
        LEFT JOIN event_schedule es ON e.EventID  = es.EventID
        LEFT JOIN venue v           ON es.VenueID = v.VenueID
        LEFT JOIN timeslot t        ON es.SlotID  = t.SlotID
        LEFT JOIN booking b         ON e.EventID  = b.EventID
        WHERE t.EventDate BETWEEN ? AND ?
          AND e.Status != 'CANCELLED'
          $venueFilter
        GROUP BY e.EventID, e.Title, e.CapacityLimit, v.Name, t.EventDate
        ORDER BY FillPct DESC
    ");
    $capAnalysis->execute($capParams);
    $capacityData = $capAnalysis->fetchAll(PDO::FETCH_ASSOC);

    // All venues for filter dropdown
    $venues = $pdo->query("
        SELECT VenueID, Name AS VenueName FROM venue WHERE IsActive = TRUE ORDER BY Name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'      => true,
        'summary'      => $summaryData,
        'venueUtil'    => $venueData,
        'capacity'     => $capacityData,
        'venues'       => $venues
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
