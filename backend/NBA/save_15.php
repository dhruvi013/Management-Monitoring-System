<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $peo_mission_matrix = $_POST['peo_mission_matrix']; // Expecting this to be a text description or JSON string
    $justification = $_POST['justification'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_15 SET academic_year=?, peo_mission_matrix=?, justification=? WHERE id=?");
        $stmt->execute([$academic_year, $peo_mission_matrix, $justification, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_15 (academic_year, peo_mission_matrix, justification) VALUES (?, ?, ?)");
        $stmt->execute([$academic_year, $peo_mission_matrix, $justification]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=1.5&msg=Data Saved Successfully");
    exit();
}
?>
