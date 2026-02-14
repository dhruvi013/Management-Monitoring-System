<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$description = trim($_POST['description'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_841 SET academic_year = :year, description = :desc WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':desc' => $description, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_841 (academic_year, description) VALUES (:year, :desc)");
        $stmt->execute([':year' => $academic_year, ':desc' => $description]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=8.4.1&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=8.4.1&msg=$msg&type=error");
}
?>
