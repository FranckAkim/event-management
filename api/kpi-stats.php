<?php
// api/kpi-stats.php - Role-aware dashboard KPI numbers
//
// TRIGGER INTEGRATION:
//   Trigger 2 (trg_booking_check_capacity) — all COUNT(booking) capacity checks now filter
//             Status IN ('PENDING','APPROVED') to match exactly what the trigger enforces.
//             Previously counting ALL bookings could inflate numbers with CANCELLED/REJECTED rows.
//   Trigger 3 (trg_venue_deactivated)      — auto-cancelled events excluded via Status != 'CANCELLED'
//             which was already in place; no extra changes needed for this trigger.

header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $role   = strtolower($_SESSION['role']   ?? 'requester');
    $userID = (int)($_SESSION['user_id']     ?? 0);

    if ($role === 'admin') {
        // ── ADMIN: system-wide ────────────────────────────────────────────

        $activeEvents = $pdo->query("
            SELECT COUNT(*) FROM event WHERE Status = 'CONFIRMED'
        ")->fetchColumn();

        // Open days next 7 days (days that have no CONFIRMED events)
        $bookedDates = $pdo->query("
            SELECT DISTINCT t.EventDate
            FROM timeslot t
            JOIN event_schedule es ON t.SlotID   = es.SlotID
            JOIN event e           ON es.EventID = e.EventID
            WHERE t.EventDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
              AND e.Status != 'CANCELLED'
        ")->fetchAll(PDO::FETCH_COLUMN);

        $openDays = 0;
        for ($i = 0; $i < 7; $i++) {
            if (!in_array(date('Y-m-d', strtotime("+$i days")), $bookedDates)) $openDays++;
        }

        // Near-capacity: count only PENDING+APPROVED bookings — matches Trigger 2
        $capacityAlerts = $pdo->query("
            SELECT COUNT(*) FROM (
                SELECT e.EventID
                FROM event e
                WHERE e.Status != 'CANCELLED'
                GROUP BY e.EventID, e.CapacityLimit
                HAVING (
                    SELECT COUNT(*) FROM booking b
                    WHERE b.EventID = e.EventID
                      AND b.Status IN ('PENDING','APPROVED')
                ) >= e.CapacityLimit * 0.85
            ) AS alerts
        ")->fetchColumn();

        // Pending resource requests
        try {
            $pendingRequests = $pdo->query("
                SELECT COUNT(*) FROM event_resource WHERE Status = 'PENDING'
            ")->fetchColumn();
        } catch (Exception $e) {
            $pendingRequests = $pdo->query("SELECT COUNT(*) FROM event_resource")->fetchColumn();
        }

        echo json_encode([
            'success'         => true,
            'role'            => 'admin',
            'activeEvents'    => (int)$activeEvents,
            'openDays'        => (int)$openDays,
            'capacityAlerts'  => (int)$capacityAlerts,
            'pendingRequests' => (int)$pendingRequests,
            'label4'          => 'Pending Resource Requests'
        ]);
    } elseif ($role === 'organiser') {
        // ── ORGANISER: their events only ──────────────────────────────────

        $myEvents = $pdo->prepare("
            SELECT COUNT(*) FROM event
            WHERE OrganizerID = ? AND Status = 'CONFIRMED'
        ");
        $myEvents->execute([$userID]);
        $activeEvents = $myEvents->fetchColumn();

        // Upcoming events this week
        $upcomingStmt = $pdo->prepare("
            SELECT COUNT(DISTINCT e.EventID)
            FROM event e
            JOIN event_schedule es ON e.EventID = es.EventID
            JOIN timeslot t        ON es.SlotID  = t.SlotID
            WHERE e.OrganizerID = ?
              AND e.Status = 'CONFIRMED'
              AND t.EventDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ");
        $upcomingStmt->execute([$userID]);
        $upcomingCount = $upcomingStmt->fetchColumn();

        // Near-capacity events — count PENDING+APPROVED only (Trigger 2 alignment)
        $capStmt = $pdo->prepare("
            SELECT COUNT(*) FROM (
                SELECT e.EventID
                FROM event e
                WHERE e.OrganizerID = ? AND e.Status != 'CANCELLED'
                GROUP BY e.EventID, e.CapacityLimit
                HAVING (
                    SELECT COUNT(*) FROM booking b
                    WHERE b.EventID = e.EventID
                      AND b.Status IN ('PENDING','APPROVED')
                ) >= e.CapacityLimit * 0.85
            ) AS alerts
        ");
        $capStmt->execute([$userID]);
        $capacityAlerts = $capStmt->fetchColumn();

        // Total active bookings on organiser's events (PENDING + APPROVED only)
        $bookingStmt = $pdo->prepare("
            SELECT COUNT(b.BookingID)
            FROM booking b
            JOIN event e ON b.EventID = e.EventID
            WHERE e.OrganizerID = ?
              AND b.Status IN ('PENDING','APPROVED')
        ");
        $bookingStmt->execute([$userID]);
        $totalBookings = $bookingStmt->fetchColumn();

        echo json_encode([
            'success'         => true,
            'role'            => 'organiser',
            'activeEvents'    => (int)$activeEvents,
            'openDays'        => (int)$upcomingCount,
            'capacityAlerts'  => (int)$capacityAlerts,
            'pendingRequests' => (int)$totalBookings,
            'label1'          => 'My Confirmed Events',
            'label2'          => 'My Events This Week',
            'label3'          => 'Near Capacity',
            'label4'          => 'Total Registrations'
        ]);
    } else {
        // ── REQUESTER/ATTENDEE ────────────────────────────────────────────

        // Count only active (PENDING + APPROVED) bookings for this user
        $myBookings = $pdo->prepare("
            SELECT COUNT(*) FROM booking
            WHERE UserID = ?
              AND Status IN ('PENDING','APPROVED')
        ");
        $myBookings->execute([$userID]);
        $totalBookings = $myBookings->fetchColumn();

        // Upcoming APPROVED events this user is registered for
        $upcomingStmt = $pdo->prepare("
            SELECT COUNT(DISTINCT b.EventID)
            FROM booking b
            JOIN event e           ON b.EventID  = e.EventID
            JOIN event_schedule es ON e.EventID  = es.EventID
            JOIN timeslot t        ON es.SlotID   = t.SlotID
            WHERE b.UserID = ?
              AND b.Status = 'APPROVED'
              AND t.EventDate >= CURDATE()
              AND e.Status = 'CONFIRMED'
        ");
        $upcomingStmt->execute([$userID]);
        $upcomingEvents = $upcomingStmt->fetchColumn();

        // Total publicly available CONFIRMED events
        $allEvents = $pdo->query("
            SELECT COUNT(*) FROM event WHERE Status = 'CONFIRMED'
        ")->fetchColumn();

        // Open days next 7 days
        $bookedDates = $pdo->query("
            SELECT DISTINCT t.EventDate
            FROM timeslot t
            JOIN event_schedule es ON t.SlotID   = es.SlotID
            JOIN event e           ON es.EventID = e.EventID
            WHERE t.EventDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
              AND e.Status = 'CONFIRMED'
        ")->fetchAll(PDO::FETCH_COLUMN);

        $openDays = 0;
        for ($i = 0; $i < 7; $i++) {
            if (!in_array(date('Y-m-d', strtotime("+$i days")), $bookedDates)) $openDays++;
        }

        echo json_encode([
            'success'         => true,
            'role'            => 'requester',
            'activeEvents'    => (int)$allEvents,
            'openDays'        => (int)$upcomingEvents,
            'capacityAlerts'  => (int)$totalBookings,
            'pendingRequests' => (int)$openDays,
            'label1'          => 'Available Events',
            'label2'          => 'My Upcoming Events',
            'label3'          => 'My Active Registrations',
            'label4'          => 'Open Days This Week'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
