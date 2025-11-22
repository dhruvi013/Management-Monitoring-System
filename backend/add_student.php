<?php
// backend/add_student.php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../frontend/index.php?msg=" . urlencode("Invalid request") . "&type=error");
    exit;
}

// Get form values
$first_name     = trim($_POST['first_name'] ?? '');
$middle_name    = trim($_POST['middle_name'] ?? '');
$last_name      = trim($_POST['last_name'] ?? '');
$gr_no          = trim($_POST['gr_no'] ?? '');
$enrollment_no  = trim($_POST['enrollment_no'] ?? '');
$class          = trim($_POST['class'] ?? '');
$semester       = (int) trim($_POST['semester'] ?? 0); // cast to int
$batch          = trim($_POST['batch'] ?? '');
$academic_year  = trim($_POST['academic_year'] ?? '');

// Check required fields
if ($first_name === '' || $last_name === '' || $gr_no === '' ||
    $enrollment_no === '' || $class === '' || $semester <= 0 || $batch === '' || $academic_year === '') {
    header("Location: ../frontend/index.php?msg=" . urlencode("Please fill all required fields") . "&type=error");
    exit;
}

try {
    // Insert into DB
    $stmt = $pdo->prepare("
        INSERT INTO students 
            (gr_no, enrollment_no, class, semester, first_name, middle_name, last_name, batch, academic_year)
        VALUES 
            (:gr_no, :enrollment_no, :class, :semester, :first_name, :middle_name, :last_name, :batch, :academic_year)
    ");

    $stmt->execute([
        ':gr_no'         => $gr_no,
        ':enrollment_no' => $enrollment_no,
        ':class'         => $class,
        ':semester'      => $semester,
        ':first_name'    => $first_name,
        ':middle_name'   => $middle_name ?: null,
        ':last_name'     => $last_name,
        ':batch'         => $batch,
        ':academic_year' => $academic_year
    ]);

    header("Location: ../frontend/index.php?msg=" . urlencode("Student added successfully") . "&type=success");
    exit;

} catch (PDOException $e) {
    // Show exact DB error for debugging
    header("Location: ../frontend/index.php?msg=" . urlencode("Insert failed: " . $e->getMessage()) . "&type=error");
    exit;
}
?>
