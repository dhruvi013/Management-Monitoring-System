<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$mentoring_system = trim($_POST['mentoring_system'] ?? '');
$efficacy = trim($_POST['efficacy'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_91 SET academic_year = :year, mentoring_system = :ms, efficacy = :eff WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':ms' => $mentoring_system, ':eff' => $efficacy, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_91 (academic_year, mentoring_system, efficacy) VALUES (:year, :ms, :eff)");
        $stmt->execute([':year' => $academic_year, ':ms' => $mentoring_system, ':eff' => $efficacy]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=9.1&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=9.1&msg=$msg&type=error");
}
?>
