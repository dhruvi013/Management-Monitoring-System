<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $peo_title = $_POST['peo_title'];
    $peo_statement = $_POST['peo_statement'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_12 SET academic_year=?, peo_title=?, peo_statement=? WHERE id=?");
        $stmt->execute([$academic_year, $peo_title, $peo_statement, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_12 (academic_year, peo_title, peo_statement) VALUES (?, ?, ?)");
        $stmt->execute([$academic_year, $peo_title, $peo_statement]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=1.2&msg=Data Saved Successfully");
    exit();
}
?>
