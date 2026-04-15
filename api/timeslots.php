<?php
// api/timeslots.php - Get list of time slots (fixed to match your actual table)
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->prepare("
        SELECT 
            SlotID,
            EventDate,
            StartTime,
            EndTime,
            CONCAT(EventDate, ' ', StartTime, ' - ', EndTime) AS display
        FROM timeslot
        ORDER BY EventDate ASC, StartTime ASC
    ");
    $stmt->execute();
    $timeslots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'slots' => $timeslots   // Changed to 'slots' to match the JS code
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
