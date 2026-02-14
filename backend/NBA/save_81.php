<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$regular_faculty = intval($_POST['regular_faculty'] ?? 0);
$contract_faculty = intval($_POST['contract_faculty'] ?? 0);
$student_count = intval($_POST['student_count'] ?? 0);

if ($academic_year === '' || $student_count <= 0) {
    $msg = urlencode("Invalid Iinput. Student count must be greater than 0.");
    header("Location: ../../frontend/nba_page.php?criteria=8.1&msg=$msg&type=error");
    exit;
}

// FYSFR Calculation
$total_faculty = $regular_faculty + $contract_faculty;
$fysfr = ($total_faculty > 0) ? ($student_count / $total_faculty) : 0; // Keeping as Students/Faculty ratio based on typical understanding, but user formula said (5 * 20) / FYSFR. Wait. Use formula: FYSFR = Student / Faculty. 

// Assessment Calculation
// Formula: (5 * 20) / FYSFR. Limited to Max 5. 
// Note: If FYSFR > 25, assessment = 0.

$assessment_score = 0;
if ($fysfr > 25) {
    $assessment_score = 0;
} else if ($fysfr > 0) {
    $assessment_score = (5 * 20) / $fysfr;
    if ($assessment_score > 5) {
        $assessment_score = 5;
    }
}

$fysfr = round($fysfr, 2);
$assessment_score = round($assessment_score, 2);

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_81 SET academic_year = :year, regular_faculty = :reg, contract_faculty = :cont, student_count = :stu, fysfr = :fysfr, assessment_score = :score WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':reg' => $regular_faculty, ':cont' => $contract_faculty, ':stu' => $student_count, ':fysfr' => $fysfr, ':score' => $assessment_score, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_81 (academic_year, regular_faculty, contract_faculty, student_count, fysfr, assessment_score) VALUES (:year, :reg, :cont, :stu, :fysfr, :score)");
        $stmt->execute([':year' => $academic_year, ':reg' => $regular_faculty, ':cont' => $contract_faculty, ':stu' => $student_count, ':fysfr' => $fysfr, ':score' => $assessment_score]);
    }
    $msg = urlencode("Saved successfully! FYSFR: $fysfr, Score: $assessment_score");
    header("Location: ../../frontend/nba_page.php?criteria=8.1&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=8.1&msg=$msg&type=error");
}
?>
