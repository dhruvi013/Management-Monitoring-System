<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$library_details = trim($_POST['library_details'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_10_4 SET academic_year = :year, library_details = :ld WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':ld' => $library_details, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_10_4 (academic_year, library_details) VALUES (:year, :ld)");
        $stmt->execute([':year' => $academic_year, ':ld' => $library_details]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=10.4&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=10.4&msg=$msg&type=error");
}
?>
