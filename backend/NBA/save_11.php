<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $vision = $_POST['vision'];
    $mission = $_POST['mission'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_11 SET academic_year=?, vision=?, mission=? WHERE id=?");
        $stmt->execute([$academic_year, $vision, $mission, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_11 (academic_year, vision, mission) VALUES (?, ?, ?)");
        $stmt->execute([$academic_year, $vision, $mission]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=1.1&msg=Data Saved Successfully");
    exit();
}
?>
