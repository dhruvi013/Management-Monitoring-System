<?php
// backend/delete.php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/index.php');
    exit;
}

$ids = $_POST['student_ids'] ?? [];
if (!is_array($ids) || count($ids) === 0) {
    header("Location: ../frontend/index.php?msg=" . urlencode("No students selected for deletion") . "&type=error");
    exit;
}

$ids = array_map('intval', $ids);

$placeholders = implode(',', array_fill(0, count($ids), '?'));

try {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    header("Location: ../frontend/index.php?msg=" . urlencode("Selected students deleted") . "&type=success");
} catch (PDOException $e) {
    header("Location: ../frontend/index.php?msg=" . urlencode("Deletion failed: " . $e->getMessage()) . "&type=error");
}
