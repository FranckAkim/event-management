<?php
// api/check-conflict.php - Real conflict detection
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $venueId = (int)($input['venueId'] ?? 0);
    $slotId = (int)($input['slotId'] ?? 0);
    $currentEventId = (int)($input['currentEventId'] ?? 0); // For editing

    if ($venueId <= 0 || $slotId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Missing venue or slot']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as conflict_count, e.Title
        FROM event_schedule es
        JOIN event e ON es.EventID = e.EventID
        WHERE es.VenueID = ? 
          AND es.SlotID = ?
          AND es.EventID != ?
    ");

    $stmt->execute([$venueId, $slotId, $currentEventId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $hasConflict = $row['conflict_count'] > 0;

    echo json_encode([
        'success' => true,
        'conflict' => $hasConflict,
        'message' => $hasConflict
            ? "Conflict detected with event: " . ($row['Title'] ?? 'Another event')
            : "No conflicts found. This slot is available."
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
