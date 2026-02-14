<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$budget_adequacy = trim($_POST['budget_adequacy'] ?? '');
$utilization = trim($_POST['utilization'] ?? '');
$audited_statements_link = trim($_POST['audited_statements_link'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_10_2 SET academic_year = :year, budget_adequacy = :ba, utilization = :uz, audited_statements_link = :asl WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':ba' => $budget_adequacy, ':uz' => $utilization, ':asl' => $audited_statements_link, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_10_2 (academic_year, budget_adequacy, utilization, audited_statements_link) VALUES (:year, :ba, :uz, :asl)");
        $stmt->execute([':year' => $academic_year, ':ba' => $budget_adequacy, ':uz' => $utilization, ':asl' => $audited_statements_link]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=10.2&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=10.2&msg=$msg&type=error");
}
?>
