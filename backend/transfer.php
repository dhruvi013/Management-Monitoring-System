<?php
// backend/transfer.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/index.php');
    exit;
}

// expecting array of student ids
$ids = $_POST['student_ids'] ?? [];
if (!is_array($ids) || count($ids) === 0) {
    header("Location: ../frontend/index.php?msg=" . urlencode("No students selected for transfer") . "&type=error");
    exit;
}

$ids = array_map('intval', $ids);

try {
    $pdo->beginTransaction();

    $select = $pdo->prepare("SELECT id, academic_year FROM students WHERE id = :id FOR UPDATE");
    $update = $pdo->prepare("UPDATE students SET academic_year = :ny WHERE id = :id");

    foreach ($ids as $id) {
        $select->execute([':id' => $id]);
        $row = $select->fetch();
        if (!$row) continue;

        $current = $row['academic_year'];
        $next = increment_academic_year($current);

        $update->execute([':ny' => $next, ':id' => $id]);
    }

    $pdo->commit();
    header("Location: ../frontend/index.php?msg=" . urlencode("Selected students transferred to next academic year") . "&type=success");
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../frontend/index.php?msg=" . urlencode("Transfer failed: " . $e->getMessage()) . "&type=error");
}
