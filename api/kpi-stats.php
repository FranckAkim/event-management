<?php
// api/kpi-stats.php - Role-aware dashboard KPI numbers
// admin    → system-wide stats
// organiser → their own events only
// requester → their registrations only
header('Content-Type: application/json');
require_once '../config/db.php';
session_start();

try {
    $role   = strtolower($_SESSION['role']   ?? 'requester');
    $userID = (int)($_SESSION['user_id']     ?? 0);

    if ($role === 'admin') {
        // ── ADMIN: system-wide ────────────────────────────────────────────

        $activeEvents = $pdo->query("
            SELECT COUNT(*) FROM event WHERE Status = 'CONFIRMED'
        ")->fetchColumn();

        // Open days next 7 days (days with no events)
        $bookedDates = $pdo->query("
            SELECT DISTINCT t.EventDate
            FROM timeslot t
            JOIN event_schedule es ON t.SlotID  = es.SlotID
            JOIN event e           ON es.EventID = e.EventID
            WHERE t.EventDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
              AND e.Status != 'CANCELLED'
        ")->fetchAll(PDO::FETCH_COLUMN);

        $openDays = 0;
        for ($i = 0; $i < 7; $i++) {
            if (!in_array(date('Y-m-d', strtotime("+$i days")), $bookedDates)) $openDays++;
        }

        $capacityAlerts = $pdo->query("
            SELECT COUNT(*) FROM (
                SELECT e.EventID
                FROM event e
                LEFT JOIN booking b ON e.EventID = b.EventID
                WHERE e.Status != 'CANCELLED'
                GROUP BY e.EventID, e.CapacityLimit
                HAVING COUNT(b.BookingID) >= e.CapacityLimit * 0.85
            ) AS alerts
        ")->fetchColumn();

        // Pending resource requests (check if Status column exists)
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

        // Upcoming events they're organising in next 7 days
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

        // Near-capacity events they organise
        $capStmt = $pdo->prepare("
            SELECT COUNT(*) FROM (
                SELECT e.EventID
                FROM event e
                LEFT JOIN booking b ON e.EventID = b.EventID
                WHERE e.OrganizerID = ? AND e.Status != 'CANCELLED'
                GROUP BY e.EventID, e.CapacityLimit
                HAVING COUNT(b.BookingID) >= e.CapacityLimit * 0.85
            ) AS alerts
        ");
        $capStmt->execute([$userID]);
        $capacityAlerts = $capStmt->fetchColumn();

        // Total bookings across their events
        $bookingStmt = $pdo->prepare("
            SELECT COUNT(b.BookingID)
            FROM booking b
            JOIN event e ON b.EventID = e.EventID
            WHERE e.OrganizerID = ?
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
        // ── REQUESTER/ATTENDEE: their registrations ───────────────────────

        $myBookings = $pdo->prepare("
            SELECT COUNT(*) FROM booking WHERE UserID = ?
        ");
        $myBookings->execute([$userID]);
        $totalBookings = $myBookings->fetchColumn();

        // Upcoming registered events
        $upcomingStmt = $pdo->prepare("
            SELECT COUNT(DISTINCT b.EventID)
            FROM booking b
            JOIN event e           ON b.EventID  = e.EventID
            JOIN event_schedule es ON e.EventID  = es.EventID
            JOIN timeslot t        ON es.SlotID   = t.SlotID
            WHERE b.UserID = ?
              AND t.EventDate >= CURDATE()
              AND e.Status = 'CONFIRMED'
        ");
        $upcomingStmt->execute([$userID]);
        $upcomingEvents = $upcomingStmt->fetchColumn();

        // Total confirmed events available to browse
        $allEvents = $pdo->query("
            SELECT COUNT(*) FROM event WHERE Status = 'CONFIRMED'
        ")->fetchColumn();

        // Open days (venues available) next 7 days
        $bookedDates = $pdo->query("
            SELECT DISTINCT t.EventDate
            FROM timeslot t
            JOIN event_schedule es ON t.SlotID = es.SlotID
            JOIN event e ON es.EventID = e.EventID
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
            'label3'          => 'My Registrations',
            'label4'          => 'Open Days This Week'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
