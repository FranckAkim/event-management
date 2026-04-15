<?php
// api/remove-resource.php
// Removes a resource assignment from an event (deletes from event_resource)
// Admin can remove any assignment
// Organiser can only remove from their own events
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $input      = json_decode(file_get_contents('php://input'), true);
    $eventID    = (int)($input['eventID']    ?? 0);
    $resourceID = (int)($input['resourceID'] ?? 0);
    $userID     = (int)($_SESSION['user_id'] ?? 0);
    $role       = strtolower($_SESSION['role'] ?? 'requester');

    // Only admin and organiser can remove resources
    if ($role === 'requester') {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to remove resources.']);
        exit;
    }

    if ($eventID <= 0 || $resourceID <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid event or resource ID.']);
        exit;
    }

    // Verify the assignment exists and check organiser ownership
    $check = $pdo->prepare("
        SELECT
            er.EventID,
            er.ResourceID,
            er.Quantity,
            e.Title         AS EventTitle,
            e.OrganizerID,
            r.Name          AS ResourceName
        FROM event_resource er
        JOIN event    e ON er.EventID    = e.EventID
        JOIN resource r ON er.ResourceID = r.ResourceID
        WHERE er.EventID = ? AND er.ResourceID = ?
        LIMIT 1
    ");
    $check->execute([$eventID, $resourceID]);
    $assignment = $check->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        echo json_encode(['success' => false, 'error' => 'Resource assignment not found.']);
        exit;
    }

    // Organiser ownership check — they can only remove from their own events
    if ($role === 'organiser' && (int)$assignment['OrganizerID'] !== $userID) {
        echo json_encode([
            'success' => false,
            'error'   => 'You can only remove resources from your own events.'
        ]);
        exit;
    }

    // Delete the assignment
    $pdo->prepare("
        DELETE FROM event_resource
        WHERE EventID = ? AND ResourceID = ?
    ")->execute([$eventID, $resourceID]);

    echo json_encode([
        'success'      => true,
        'message'      => "\"{$assignment['ResourceName']}\" removed from \"{$assignment['EventTitle']}\".",
        'eventID'      => $eventID,
        'resourceID'   => $resourceID
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
