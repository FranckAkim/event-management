<?php
// api/event-health.php
// Returns event health overview for the admin dashboard panel:
//   - Total confirmed events this month
//   - Near-capacity events (>= 85% full)
//   - Empty events (0 registrations)
//   - Most popular event (highest fill %)
//   - Least popular event (lowest fill % with capacity > 0)
//   - Average fill rate across all confirmed events
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

    // ── All confirmed events with active booking counts ───────────────
    $stmt = $pdo->query("
        SELECT
            e.EventID,
            e.Title         AS EventName,
            e.CapacityLimit AS Capacity,
            e.Status,
            t.EventDate,
            v.Name          AS VenueName,
            (SELECT COUNT(*) FROM booking b
             WHERE b.EventID = e.EventID
               AND b.Status IN ('PENDING','APPROVED')) AS Registered
        FROM event e
        LEFT JOIN event_schedule es ON e.EventID  = es.EventID
        LEFT JOIN timeslot t        ON es.SlotID   = t.SlotID
        LEFT JOIN venue v           ON es.VenueID  = v.VenueID
        WHERE e.Status = 'CONFIRMED'
        ORDER BY t.EventDate ASC
    ");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalConfirmed = count($events);
    $thisMonth      = 0;
    $nearCapacity   = [];
    $emptyEvents    = [];
    $fillRates      = [];
    $mostPopular    = null;
    $leastPopular   = null;
    $bestFill       = -1;
    $worstFill      = 101;

    $monthStart = date('Y-m-01');
    $monthEnd   = date('Y-m-t');

    foreach ($events as $ev) {
        $cap       = (int)$ev['Capacity'];
        $reg       = (int)$ev['Registered'];
        $fillPct   = $cap > 0 ? round(($reg / $cap) * 100, 1) : 0;

        // This month
        if ($ev['EventDate'] >= $monthStart && $ev['EventDate'] <= $monthEnd) {
            $thisMonth++;
        }

        // Near capacity (>= 85%)
        if ($fillPct >= 85) {
            $nearCapacity[] = [
                'EventName' => $ev['EventName'],
                'FillPct'   => $fillPct,
                'Registered' => $reg,
                'Capacity'  => $cap,
                'VenueName' => $ev['VenueName'],
                'EventDate' => $ev['EventDate']
            ];
        }

        // Empty events (0 registrations)
        if ($reg === 0) {
            $emptyEvents[] = [
                'EventName' => $ev['EventName'],
                'VenueName' => $ev['VenueName'],
                'EventDate' => $ev['EventDate'],
                'Capacity'  => $cap
            ];
        }

        // Track fill rates for average
        if ($cap > 0) {
            $fillRates[] = $fillPct;

            // Most popular
            if ($fillPct > $bestFill) {
                $bestFill    = $fillPct;
                $mostPopular = [
                    'EventName' => $ev['EventName'],
                    'FillPct'   => $fillPct,
                    'Registered' => $reg,
                    'Capacity'  => $cap
                ];
            }

            // Least popular (only count future/today events)
            if ($ev['EventDate'] >= date('Y-m-d') && $fillPct < $worstFill) {
                $worstFill    = $fillPct;
                $leastPopular = [
                    'EventName' => $ev['EventName'],
                    'FillPct'   => $fillPct,
                    'Registered' => $reg,
                    'Capacity'  => $cap,
                    'EventDate' => $ev['EventDate']
                ];
            }
        }
    }

    $avgFill = count($fillRates) > 0
        ? round(array_sum($fillRates) / count($fillRates), 1)
        : 0;

    // Sort near-capacity by fill % desc
    usort($nearCapacity, fn($a, $b) => $b['FillPct'] <=> $a['FillPct']);

    echo json_encode([
        'success'        => true,
        'totalConfirmed' => $totalConfirmed,
        'thisMonth'      => $thisMonth,
        'nearCapacity'   => $nearCapacity,
        'nearCapacityCount' => count($nearCapacity),
        'emptyEvents'    => $emptyEvents,
        'emptyCount'     => count($emptyEvents),
        'mostPopular'    => $mostPopular,
        'leastPopular'   => $leastPopular,
        'avgFill'        => $avgFill
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
