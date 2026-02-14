<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$po_pso_name = trim($_POST['po_pso_name'] ?? '');
$attainment_level = floatval($_POST['attainment_level'] ?? 0);
$target_level = floatval($_POST['target_level'] ?? 0);

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_851 SET academic_year = :year, po_pso_name = :name, attainment_level = :att, target_level = :target WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':name' => $po_pso_name, ':att' => $attainment_level, ':target' => $target_level, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_851 (academic_year, po_pso_name, attainment_level, target_level) VALUES (:year, :name, :att, :target)");
        $stmt->execute([':year' => $academic_year, ':name' => $po_pso_name, ':att' => $attainment_level, ':target' => $target_level]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=8.5.1&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=8.5.1&msg=$msg&type=error");
}
?>
