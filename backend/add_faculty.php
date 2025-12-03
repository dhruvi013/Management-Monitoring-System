<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../frontend/index.php?msg=" . urlencode("Invalid request") . "&type=error");
    exit;
}

$faculty_id   = trim($_POST['faculty_id'] ?? '');
$first_name   = trim($_POST['first_name'] ?? '');
$middle_name  = trim($_POST['middle_name'] ?? '');
$last_name    = trim($_POST['last_name'] ?? '');
$department   = trim($_POST['department'] ?? '');
$designation  = trim($_POST['designation'] ?? '');
$academic_year = trim($_POST['academic_year'] ?? '');

if ($faculty_id === '' || $first_name === '' || $last_name === '' ||
    $department === '' || $designation === '' || $academic_year === '') {

    header("Location: ../frontend/index.php?msg=" . urlencode("Please fill all required fields") . "&type=error");
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO faculty 
            (faculty_id, first_name, middle_name, last_name, department, designation, academic_year)
        VALUES 
            (:id, :fn, :mn, :ln, :dept, :des, :ay)
    ");

    $stmt->execute([
        ':id'   => $faculty_id,
        ':fn'   => $first_name,
        ':mn'   => $middle_name ?: null,
        ':ln'   => $last_name,
        ':dept' => $department,
        ':des'  => $designation,
        ':ay'   => $academic_year
    ]);

    header("Location: ../frontend/index.php?msg=" . urlencode("Faculty added successfully") . "&type=success");
    exit;

} catch (PDOException $e) {
    header("Location: ../frontend/index.php?msg=" . urlencode("Insert failed: " . $e->getMessage()) . "&type=error");
    exit;
}
?>
