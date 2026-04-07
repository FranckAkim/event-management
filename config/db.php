<?php
// config/db.php
$host     = 'localhost';
$port     = '3306';           // ← Your XAMPP MySQL port
$dbname   = 'event_management';
$username = 'root';           // default XAMPP user
$password = '';               // default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
