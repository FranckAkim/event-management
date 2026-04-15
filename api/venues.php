<?php
// api/venues.php - Fixed to match your actual venue table
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->prepare("
        SELECT 
            VenueID, 
            Name AS VenueName,   -- Alias so JS code still works
            MaxCapacity 
        FROM venue 
        WHERE IsActive = 1 
        ORDER BY Name ASC
    ");
    $stmt->execute();
    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'venues' => $venues
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
