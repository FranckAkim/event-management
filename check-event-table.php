<?php
require_once 'config/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM event");
echo "<h2>Columns in 'event' table:</h2><ul>";
while ($row = $stmt->fetch()) {
    echo "<li><strong>" . htmlspecialchars($row['Field']) . "</strong> (" . $row['Type'] . ")</li>";
}
echo "</ul>";
