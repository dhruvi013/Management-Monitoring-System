<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $po_pso_name = $_POST['po_pso_name'];
    $target_level = $_POST['target_level'];
    $attainment_level = $_POST['attainment_level'];
    $observations = $_POST['observations'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_332 SET academic_year=?, semester=?, po_pso_name=?, target_level=?, attainment_level=?, observations=? WHERE id=?");
        $stmt->execute([$academic_year, $semester, $po_pso_name, $target_level, $attainment_level, $observations, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_332 (academic_year, semester, po_pso_name, target_level, attainment_level, observations) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$academic_year, $semester, $po_pso_name, $target_level, $attainment_level, $observations]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=3.3.2&msg=Data Saved Successfully");
    exit();
}
?>
