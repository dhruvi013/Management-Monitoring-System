<?php
// backend/db.php
// Database connection using PDO
// Detect environment (Vercel or Local)
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'department_monitoring';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

// On Vercel (or other clouds), sometimes the port is separate or SSL is needed.
// For example, Aiven or PlanetScale might require SSL.
// But for basic usage, the above environment variables are the standard starting point.

// $conn = new mysqli($host, $user, $pass, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }



$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production, avoid echoing exception message.
    die("Database connection failed: " . $e->getMessage());
}
