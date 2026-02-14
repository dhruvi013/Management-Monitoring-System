<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$facilities_feedback = trim($_POST['facilities_feedback'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_93 SET academic_year = :year, facilities_feedback = :ff WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':ff' => $facilities_feedback, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_93 (academic_year, facilities_feedback) VALUES (:year, :ff)");
        $stmt->execute([':year' => $academic_year, ':ff' => $facilities_feedback]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=9.3&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=9.3&msg=$msg&type=error");
}
?>
