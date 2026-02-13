<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $process = $_POST['process'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_14 SET academic_year=?, process=? WHERE id=?");
        $stmt->execute([$academic_year, $process, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_14 (academic_year, process) VALUES (?, ?)");
        $stmt->execute([$academic_year, $process]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=1.4&msg=Data Saved Successfully");
    exit();
}
?>
