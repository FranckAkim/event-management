<?php
// api/resources-data.php
// Actual schema confirmed from phpMyAdmin:
//   resource(ResourceID, Name, Type)
//   event_resource(EventID, ResourceID, Quantity)  ← no Status, no EventResourceID
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // ── Resource inventory with allocation totals ──────────────────────────
    // Since there's no Status column, just sum all quantities per resource
    $inventory = $pdo->query("
        SELECT
            r.ResourceID,
            r.Name          AS ResourceName,
            r.Type          AS ResourceType,
            COALESCE(SUM(er.Quantity), 0)           AS TotalAllocated,
            COUNT(DISTINCT er.EventID)               AS EventsUsing,
            CASE
                WHEN COUNT(DISTINCT er.EventID) >= 5 THEN 'danger'
                WHEN COUNT(DISTINCT er.EventID) >= 3 THEN 'warn'
                ELSE 'ok'
            END AS AvailStatus
        FROM resource r
        LEFT JOIN event_resource er ON r.ResourceID = er.ResourceID
        GROUP BY r.ResourceID, r.Name, r.Type
        ORDER BY r.Type ASC, r.Name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ── All resource requests (linked to events) ───────────────────────────
    $requests = $pdo->query("
        SELECT
            er.EventID,
            er.ResourceID,
            er.Quantity,
            e.Title         AS EventName,
            e.Status        AS EventStatus,
            r.Name          AS ResourceName,
            r.Type          AS ResourceType,
            v.Name          AS VenueName,
            t.EventDate,
            CONCAT(COALESCE(t.StartTime,''), '–', COALESCE(t.EndTime,'')) AS TimeSlot
        FROM event_resource er
        JOIN event e    ON er.EventID    = e.EventID
        JOIN resource r ON er.ResourceID = r.ResourceID
        LEFT JOIN event_schedule es ON e.EventID  = es.EventID
        LEFT JOIN venue v           ON es.VenueID = v.VenueID
        LEFT JOIN timeslot t        ON es.SlotID   = t.SlotID
        ORDER BY t.EventDate ASC, e.Title ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ── Events for form dropdown ───────────────────────────────────────────
    $events = $pdo->query("
        SELECT EventID, Title FROM event
        WHERE Status != 'CANCELLED'
        ORDER BY Title ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ── Resources for form dropdown ────────────────────────────────────────
    $resources = $pdo->query("
        SELECT ResourceID, Name, Type FROM resource
        ORDER BY Type ASC, Name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'   => true,
        'inventory' => $inventory,
        'requests'  => $requests,
        'events'    => $events,
        'resources' => $resources
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
