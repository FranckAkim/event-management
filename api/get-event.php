<?php
// api/get-event.php - Load single event for editing
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $eventID = (int)($_GET['eventID'] ?? 0);

    if ($eventID <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid Event ID']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            e.EventID,
            e.Title,
            e.Description,
            e.CapacityLimit,
            e.Status,
            es.VenueID,
            es.SlotID
        FROM event e
        LEFT JOIN event_schedule es ON e.EventID = es.EventID
        WHERE e.EventID = ?
    ");
    $stmt->execute([$eventID]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        echo json_encode([
            'success' => true,
            'event' => $event
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Event not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
