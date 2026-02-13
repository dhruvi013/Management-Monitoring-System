<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $description = $_POST['description'];
    $marks = 0;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_225 SET academic_year=?, description=? WHERE id=?");
        $stmt->execute([$academic_year, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_225 (academic_year, description, marks) VALUES (?, ?, ?)");
        $stmt->execute([$academic_year, $description, $marks]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=2.2.5&msg=Data Saved Successfully");
    exit();
}
?>
