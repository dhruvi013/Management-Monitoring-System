<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$initiatives = trim($_POST['initiatives'] ?? '');
$benefitted_students = trim($_POST['benefitted_students'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_96 SET academic_year = :year, initiatives = :ini, benefitted_students = :ben WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':ini' => $initiatives, ':ben' => $benefitted_students, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_96 (academic_year, initiatives, benefitted_students) VALUES (:year, :ini, :ben)");
        $stmt->execute([':year' => $academic_year, ':ini' => $initiatives, ':ben' => $benefitted_students]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=9.6&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=9.6&msg=$msg&type=error");
}
?>
