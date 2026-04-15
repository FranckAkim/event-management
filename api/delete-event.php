<?php
// api/delete-event.php - Delete event with ownership enforcement
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $input       = json_decode(file_get_contents('php://input'), true);
    $eventID     = (int)($input['eventID']    ?? 0);
    $organizerID = (int)($_SESSION['user_id'] ?? 0);
    $role        = strtolower($_SESSION['role'] ?? 'requester');

    if ($eventID <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
        exit;
    }

    // Only admin can delete — organisers cannot delete any events
    if ($role !== 'admin') {
        echo json_encode([
            'success' => false,
            'error'   => 'Only administrators can delete events. Contact your admin to remove an event.'
        ]);
        exit;
    }

    // Verify event exists
    $check = $pdo->prepare("SELECT EventID FROM event WHERE EventID = ?");
    $check->execute([$eventID]);
    if (!$check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Event not found.']);
        exit;
    }

    $pdo->beginTransaction();

    // Delete related records first (foreign key order)
    $pdo->prepare("DELETE FROM event_resource WHERE EventID = ?")->execute([$eventID]);
    $pdo->prepare("DELETE FROM booking        WHERE EventID = ?")->execute([$eventID]);
    $pdo->prepare("DELETE FROM event_schedule WHERE EventID = ?")->execute([$eventID]);
    $pdo->prepare("DELETE FROM event          WHERE EventID = ?")->execute([$eventID]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Event deleted successfully.']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
