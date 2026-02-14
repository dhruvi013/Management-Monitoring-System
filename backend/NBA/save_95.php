<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$career_guidance = trim($_POST['career_guidance'] ?? '');
$counseling = trim($_POST['counseling'] ?? '');
$training = trim($_POST['training'] ?? '');
$placement_support = trim($_POST['placement_support'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_95 SET academic_year = :year, career_guidance = :cg, counseling = :coun, training = :tr, placement_support = :ps WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':cg' => $career_guidance, ':coun' => $counseling, ':tr' => $training, ':ps' => $placement_support, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_95 (academic_year, career_guidance, counseling, training, placement_support) VALUES (:year, :cg, :coun, :tr, :ps)");
        $stmt->execute([':year' => $academic_year, ':cg' => $career_guidance, ':coun' => $counseling, ':tr' => $training, ':ps' => $placement_support]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=9.5&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=9.5&msg=$msg&type=error");
}
?>
