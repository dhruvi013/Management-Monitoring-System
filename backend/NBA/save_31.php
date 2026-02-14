<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $subject_code = $_POST['subject_code'];
    $course_name = $_POST['course_name'];
    $cos_defined = $_POST['cos_defined'];
    $cos_embedded = $_POST['cos_embedded'];
    $articulation_matrix_co = $_POST['articulation_matrix_co'];
    $articulation_matrix_po = $_POST['articulation_matrix_po'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_31 SET academic_year=?, semester=?, subject_code=?, course_name=?, cos_defined=?, cos_embedded=?, articulation_matrix_co=?, articulation_matrix_po=? WHERE id=?");
        $stmt->execute([$academic_year, $semester, $subject_code, $course_name, $cos_defined, $cos_embedded, $articulation_matrix_co, $articulation_matrix_po, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_31 (academic_year, semester, subject_code, course_name, cos_defined, cos_embedded, articulation_matrix_co, articulation_matrix_po) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$academic_year, $semester, $subject_code, $course_name, $cos_defined, $cos_embedded, $articulation_matrix_co, $articulation_matrix_po]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=3.1&msg=Data Saved Successfully");
    exit();
}
?>
