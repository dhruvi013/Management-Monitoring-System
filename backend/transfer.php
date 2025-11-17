<?php
// backend/transfer.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/index.php');
    exit;
}

$batch = trim($_POST['batch'] ?? '');
$action = trim($_POST['action'] ?? '');  // "transfer" or "no_transfer"

if ($batch === '') {
    header("Location: ../frontend/index.php?msg=" . urlencode("Please select a batch") . "&type=error");
    exit;
}

if ($action !== "transfer" && $action !== "no_transfer") {
    header("Location: ../frontend/index.php?msg=" . urlencode("Invalid action selected") . "&type=error");
    exit;
}

try {
    if ($action === "no_transfer") {
        // ❌ User chose not to transfer — do nothing
        header("Location: ../frontend/index.php?msg=" . urlencode("Students NOT transferred. Batch remains unchanged.") . "&type=success");
        exit;
    }

    // ✔ User selected TRANSFER — we auto-increment academic years
    $pdo->beginTransaction();

    // Get all students in this batch
    $select = $pdo->prepare("SELECT id, academic_year FROM students WHERE batch = :batch FOR UPDATE");
    $select->execute([':batch' => $batch]);
    $students = $select->fetchAll();

    if (empty($students)) {
        $pdo->rollBack();
        header("Location: ../frontend/index.php?msg=" . urlencode("No students found in this batch") . "&type=error");
        exit;
    }

    $update = $pdo->prepare("UPDATE students SET academic_year = :ay WHERE id = :id");

    foreach ($students as $stu) {
        $current = $stu['academic_year'];
        $next = increment_academic_year($current);

        $update->execute([
            ':ay' => $next,
            ':id' => $stu['id']
        ]);
    }

    $pdo->commit();
    header("Location: ../frontend/index.php?msg=" . urlencode("Batch '$batch' transferred to next academic year") . "&type=success");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../frontend/index.php?msg=" . urlencode("Transfer failed: " . $e->getMessage()) . "&type=error");
    exit;
}
