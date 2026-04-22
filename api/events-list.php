<?php
// api/events-list.php - Role-based event filtering with registration status
//
// TRIGGER INTEGRATION:
//   Trigger 2 (trg_booking_check_capacity) — capacity counts use Status IN ('PENDING','APPROVED')
//   Trigger 3 (trg_venue_deactivated)      — CANCELLED events hidden from requesters automatically
//
// DUPLICATE PREVENTION:
//   Uses MIN() on schedule/timeslot columns so that even if event_schedule has
//   duplicate entries for the same event, only one row is returned per event.

header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $role   = strtolower($_SESSION['role']   ?? 'requester');
    $userID = (int)($_SESSION['user_id']     ?? 0);

    $params = [];
    $where  = "";

    if ($role === 'organiser') {
        $where    = "WHERE e.OrganizerID = ?";
        $params[] = $userID;
    } elseif ($role === 'requester') {
        // Show only CONFIRMED public events OR private events the user is invited to
        $where    = "WHERE e.Status = 'CONFIRMED'
          AND (
              e.IsPrivate = 0
              OR EXISTS (
                  SELECT 1 FROM booking bInv
                  WHERE bInv.EventID = e.EventID
                    AND bInv.UserID  = ?
              )
          )";
        $params[] = $userID;
    }

    $registrationSubquery = $role === 'requester'
        ? ",(SELECT COUNT(*)
               FROM booking bx
               WHERE bx.EventID = e.EventID
                 AND bx.UserID = $userID
                 AND bx.Status IN ('PENDING','APPROVED')
           ) AS IsRegistered,
           (SELECT bx2.Status
               FROM booking bx2
               WHERE bx2.EventID = e.EventID
                 AND bx2.UserID = $userID
               LIMIT 1
           ) AS RegistrationStatus"
        : ",0 AS IsRegistered, NULL AS RegistrationStatus";

    $stmt = $pdo->prepare("
        SELECT
            e.EventID,
            e.Title         AS EventName,
            e.Description,
            e.CapacityLimit AS Capacity,
            e.Status,
            e.IsPrivate,
            e.OrganizerID,
            u.Name          AS Organizer,

            -- Use MIN() on venue/slot so duplicate event_schedule rows collapse to one
            MIN(v.Name)     AS VenueName,
            MIN(v.VenueID)  AS VenueID,
            MIN(t.SlotID)   AS SlotID,
            MIN(t.EventDate) AS EventDate,
            CONCAT(
                COALESCE(MIN(t.StartTime), ''),
                '–',
                COALESCE(MIN(t.EndTime), '')
            ) AS TimeSlot,

            -- Capacity counts: PENDING + APPROVED only (matches Trigger 2)
            (SELECT COUNT(*)
             FROM booking bc
             WHERE bc.EventID = e.EventID
               AND bc.Status IN ('PENDING','APPROVED')
            ) AS Registered,

            CASE
                WHEN (SELECT COUNT(*) FROM booking bc
                      WHERE bc.EventID = e.EventID
                        AND bc.Status IN ('PENDING','APPROVED')
                     ) >= e.CapacityLimit * 0.95 THEN 'danger'
                WHEN (SELECT COUNT(*) FROM booking bc
                      WHERE bc.EventID = e.EventID
                        AND bc.Status IN ('PENDING','APPROVED')
                     ) >= e.CapacityLimit * 0.85 THEN 'warn'
                ELSE 'ok'
            END AS CapacityStatus

            $registrationSubquery

        FROM event e
        LEFT JOIN user u            ON e.OrganizerID = u.UserID
        LEFT JOIN event_schedule es ON e.EventID     = es.EventID
        LEFT JOIN venue v           ON es.VenueID    = v.VenueID
        LEFT JOIN timeslot t        ON es.SlotID     = t.SlotID
        $where
        GROUP BY
            e.EventID,
            e.Title,
            e.Description,
            e.CapacityLimit,
            e.Status,
            e.IsPrivate,
            e.OrganizerID,
            u.Name
        ORDER BY MIN(t.EventDate) ASC, MIN(t.StartTime) ASC
    ");

    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($events as &$ev) {
        $ev['IsRegistered'] = (bool)($ev['IsRegistered'] ?? false);
    }
    unset($ev);

    echo json_encode([
        'success' => true,
        'events'  => $events,
        'role'    => $role,
        'userID'  => $userID
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
