<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $subject_code = $_POST['subject_code'];
    $course_name = $_POST['course_name'];
    $attainment_level = $_POST['attainment_level'];
    $target_level = $_POST['target_level'];
    $observations = $_POST['observations'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_322 SET academic_year=?, semester=?, subject_code=?, course_name=?, attainment_level=?, target_level=?, observations=? WHERE id=?");
        $stmt->execute([$academic_year, $semester, $subject_code, $course_name, $attainment_level, $target_level, $observations, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_322 (academic_year, semester, subject_code, course_name, attainment_level, target_level, observations) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$academic_year, $semester, $subject_code, $course_name, $attainment_level, $target_level, $observations]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=3.2.2&msg=Data Saved Successfully");
    exit();
}
?>
