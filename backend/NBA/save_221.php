<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $description = $_POST['description'];
    $marks = 0;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_221 SET academic_year=?, description=? WHERE id=?");
        $stmt->execute([$academic_year, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_221 (academic_year, description, marks) VALUES (?, ?, ?)");
        $stmt->execute([$academic_year, $description, $marks]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=2.2.1&msg=Data Saved Successfully");
    exit();
}
?>
