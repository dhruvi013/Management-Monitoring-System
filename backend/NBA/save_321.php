<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $assessment_tools = $_POST['assessment_tools'];
    $quality_relevance = $_POST['quality_relevance'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_321 SET academic_year=?, semester=?, assessment_tools=?, quality_relevance=? WHERE id=?");
        $stmt->execute([$academic_year, $semester, $assessment_tools, $quality_relevance, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_321 (academic_year, semester, assessment_tools, quality_relevance) VALUES (?, ?, ?, ?)");
        $stmt->execute([$academic_year, $semester, $assessment_tools, $quality_relevance]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=3.2.1&msg=Data Saved Successfully");
    exit();
}
?>
