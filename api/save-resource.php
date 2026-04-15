<?php
// api/save-resource.php - Assign a resource to an event
// Schema: event_resource(EventID, ResourceID, Quantity)
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $input      = json_decode(file_get_contents('php://input'), true);
    $eventId    = (int)($input['eventId']    ?? 0);
    $resourceId = (int)($input['resourceId'] ?? 0);
    $quantity   = (int)($input['quantity']   ?? 0);

    if ($eventId <= 0 || $resourceId <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    // Check if this resource is already assigned to this event
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM event_resource
        WHERE EventID = ? AND ResourceID = ?
    ");
    $check->execute([$eventId, $resourceId]);

    if ($check->fetchColumn() > 0) {
        // Update existing assignment
        $stmt = $pdo->prepare("
            UPDATE event_resource SET Quantity = ?
            WHERE EventID = ? AND ResourceID = ?
        ");
        $stmt->execute([$quantity, $eventId, $resourceId]);
        echo json_encode(['success' => true, 'message' => 'Resource quantity updated.']);
    } else {
        // Insert new assignment
        $stmt = $pdo->prepare("
            INSERT INTO event_resource (EventID, ResourceID, Quantity)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$eventId, $resourceId, $quantity]);
        echo json_encode(['success' => true, 'message' => 'Resource assigned successfully.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
