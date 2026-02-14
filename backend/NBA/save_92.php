<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$feedback_process = trim($_POST['feedback_process'] ?? '');
$corrective_measures = trim($_POST['corrective_measures'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_92 SET academic_year = :year, feedback_process = :fp, corrective_measures = :cm WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':fp' => $feedback_process, ':cm' => $corrective_measures, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_92 (academic_year, feedback_process, corrective_measures) VALUES (:year, :fp, :cm)");
        $stmt->execute([':year' => $academic_year, ':fp' => $feedback_process, ':cm' => $corrective_measures]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=9.2&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=9.2&msg=$msg&type=error");
}
?>
