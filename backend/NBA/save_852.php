<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$po_pso_name = trim($_POST['po_pso_name'] ?? '');
$action_taken = trim($_POST['action_taken'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_852 SET academic_year = :year, po_pso_name = :name, action_taken = :action WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':name' => $po_pso_name, ':action' => $action_taken, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_852 (academic_year, po_pso_name, action_taken) VALUES (:year, :name, :action)");
        $stmt->execute([':year' => $academic_year, ':name' => $po_pso_name, ':action' => $action_taken]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=8.5.2&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=8.5.2&msg=$msg&type=error");
}
?>
