<?php
// api/activity-feed.php
// Returns the last 15 system activities across bookings and events
// Used by the admin Reports tab Recent Activity Feed panel
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

    // ── Booking activity (registrations, approvals, cancellations) ──
    $bookings = $pdo->query("
        SELECT
            'booking'               AS ActivityType,
            b.BookingID             AS RefID,
            u.Name                  AS ActorName,
            e.Title                 AS EventTitle,
            b.Status                AS BookingStatus,
            b.RequestedAt           AS ActivityTime,
            CASE b.Status
                WHEN 'PENDING'   THEN CONCAT(u.Name, ' registered for \"', e.Title, '\"')
                WHEN 'APPROVED'  THEN CONCAT(u.Name, '\'s booking was approved for \"', e.Title, '\"')
                WHEN 'REJECTED'  THEN CONCAT(u.Name, '\'s booking was declined for \"', e.Title, '\"')
                WHEN 'CANCELLED' THEN CONCAT(u.Name, ' cancelled their spot at \"', e.Title, '\"')
                ELSE CONCAT(u.Name, ' updated booking for \"', e.Title, '\"')
            END                     AS Description,
            CASE b.Status
                WHEN 'APPROVED'  THEN 'ok'
                WHEN 'PENDING'   THEN 'warn'
                WHEN 'REJECTED'  THEN 'danger'
                WHEN 'CANCELLED' THEN 'danger'
                ELSE 'ok'
            END                     AS BadgeClass,
            CASE b.Status
                WHEN 'PENDING'   THEN '📋'
                WHEN 'APPROVED'  THEN '✅'
                WHEN 'REJECTED'  THEN '❌'
                WHEN 'CANCELLED' THEN '🚫'
                ELSE '📋'
            END                     AS Icon
        FROM booking b
        JOIN user  u ON b.UserID  = u.UserID
        JOIN event e ON b.EventID = e.EventID
        WHERE b.RequestedAt IS NOT NULL
        ORDER BY b.RequestedAt DESC
        LIMIT 20
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ── Merge and sort by time, take top 15 ──────────────────────────
    $all = $bookings;

    usort($all, function ($a, $b) {
        return strtotime($b['ActivityTime']) - strtotime($a['ActivityTime']);
    });

    $activities = array_slice($all, 0, 15);

    // ── Format times as relative (e.g. "2 mins ago") ─────────────────
    foreach ($activities as &$act) {
        $diff = time() - strtotime($act['ActivityTime']);
        if ($diff < 60)          $act['TimeAgo'] = 'just now';
        elseif ($diff < 3600)    $act['TimeAgo'] = floor($diff / 60) . ' min' . (floor($diff / 60) > 1 ? 's' : '') . ' ago';
        elseif ($diff < 86400)   $act['TimeAgo'] = floor($diff / 3600) . ' hr' . (floor($diff / 3600) > 1 ? 's' : '') . ' ago';
        elseif ($diff < 604800)  $act['TimeAgo'] = floor($diff / 86400) . ' day' . (floor($diff / 86400) > 1 ? 's' : '') . ' ago';
        else                     $act['TimeAgo'] = date('M j', strtotime($act['ActivityTime']));
    }
    unset($act);

    echo json_encode([
        'success'    => true,
        'activities' => $activities,
        'total'      => count($activities)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
