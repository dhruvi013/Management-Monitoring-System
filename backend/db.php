<?php
// backend/db.php
// Database connection using PDO

$DB_HOST = 'localhost';
$DB_NAME = 'department_monitoring'; // change if you used different DB name
$DB_USER = 'root';                  // change as per your MySQL user
$DB_PASS = '';                      // change as needed


// $host = "localhost";
// $user = "ictmu6ya_deprtmentmonitoringsystem";
// $pass = "ictmu6ya_deprtmentmonitoringsystem";
// $dbname = "ictmu6ya_deprtmentmonitoringsystem";

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
