<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Get form data
$academic_year = trim($_POST['academic_year'] ?? '');
$admitted_degree = intval($_POST['admitted_degree'] ?? 0);
$admitted_d2d = intval($_POST['admitted_d2d'] ?? 0);
$graduated_wo_back = intval($_POST['graduated_wo_back'] ?? 0);
$graduated_w_back = intval($_POST['graduated_w_back'] ?? 0);
$id_421 = $_POST['id_421'] ?? null;
$id_422 = $_POST['id_422'] ?? null;

// Validate input
if ($academic_year === '' || $admitted_degree < 0 || $admitted_d2d < 0 || $graduated_wo_back < 0 || $graduated_w_back < 0) {
    header("Location: ../../frontend/nba_page.php?criteria=" . urlencode('4.2 - Success Rate in the Stipulated Period of Program (20)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// Calculate total admitted
$total_admitted = $admitted_degree + $admitted_d2d;

if ($total_admitted == 0) {
    header("Location: ../../frontend/nba_page.php?criteria=" . urlencode('4.2 - Success Rate in the Stipulated Period of Program (20)') . "&msg=" . urlencode("Total admitted cannot be zero") . "&type=error");
    exit;
}

// ============================================
// 4.2.1 - Success Rate WITHOUT Backlog (15 marks)
// ============================================

// Calculate success index for 4.2.1
$success_index_421 = $graduated_wo_back / $total_admitted;

// Check/Insert/Update 4.2.1
if (!empty($_POST['id_421'])) {
    $stmt = $pdo->prepare("UPDATE nba_success_421 SET academic_year=:year, admitted_degree=:deg, admitted_d2d=:d2d, total_admitted=:total, graduated_wo_back=:grad, success_index=:si WHERE id=:id");
    $stmt->execute([
        ':year' => $academic_year,
        ':deg' => $admitted_degree,
        ':d2d' => $admitted_d2d,
        ':total' => $total_admitted,
        ':grad' => $graduated_wo_back,
        ':si' => $success_index_421,
        ':id' => $_POST['id_421']
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO nba_success_421 (academic_year, admitted_degree, admitted_d2d, total_admitted, graduated_wo_back, success_index, marks) VALUES (:year, :deg, :d2d, :total, :grad, :si, 0)");
    $stmt->execute([
        ':year' => $academic_year,
        ':deg' => $admitted_degree,
        ':d2d' => $admitted_d2d,
        ':total' => $total_admitted,
        ':grad' => $graduated_wo_back,
        ':si' => $success_index_421
    ]);
}

// Get last 3 batches for 4.2.1
$stmt = $pdo->query("SELECT success_index FROM nba_success_421 ORDER BY created_at DESC LIMIT 3");
$si_421 = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Calculate average SI for 4.2.1
$avg_si_421 = 0;
if (count($si_421) > 0) {
    $avg_si_421 = array_sum($si_421) / count($si_421);
}

// Calculate marks for 4.2.1: 15 × Average SI
$marks_421 = 15 * $avg_si_421;

// Update marks for all 4.2.1 entries
$pdo->prepare("UPDATE nba_success_421 SET marks = :marks")->execute([':marks' => $marks_421]);

// ============================================
// 4.2.2 - Success Rate in Stipulated Period (5 marks)
// ============================================

// Calculate success index for 4.2.2
// graduated_w_back represents students who graduated in stipulated time
$success_index_422 = $graduated_w_back / $total_admitted;

// Check/Insert/Update 4.2.2
if (!empty($_POST['id_422'])) {
    $stmt = $pdo->prepare("UPDATE nba_success_422 SET academic_year=:year, admitted_degree=:deg, admitted_d2d=:d2d, total_admitted=:total, graduated_w_back=:grad, success_index=:si WHERE id=:id");
    $stmt->execute([
        ':year' => $academic_year,
        ':deg' => $admitted_degree,
        ':d2d' => $admitted_d2d,
        ':total' => $total_admitted,
        ':grad' => $graduated_w_back,
        ':si' => $success_index_422,
        ':id' => $_POST['id_422']
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO nba_success_422 (academic_year, admitted_degree, admitted_d2d, total_admitted, graduated_w_back, success_index, marks) VALUES (:year, :deg, :d2d, :total, :grad, :si, 0)");
    $stmt->execute([
        ':year' => $academic_year,
        ':deg' => $admitted_degree,
        ':d2d' => $admitted_d2d,
        ':total' => $total_admitted,
        ':grad' => $graduated_w_back,
        ':si' => $success_index_422
    ]);
}

// Get last 3 batches for 4.2.2
$stmt = $pdo->query("SELECT success_index FROM nba_success_422 ORDER BY created_at DESC LIMIT 3");
$si_422 = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Calculate average SI for 4.2.2
$avg_si_422 = 0;
if (count($si_422) > 0) {
    $avg_si_422 = array_sum($si_422) / count($si_422);
}

// Calculate marks for 4.2.2: 5 × Average SI
$marks_422 = 5 * $avg_si_422;

// Update marks for all 4.2.2 entries
$pdo->prepare("UPDATE nba_success_422 SET marks = :marks")->execute([':marks' => $marks_422]);

// Calculate total marks for 4.2
$total_marks_42 = $marks_421 + $marks_422;

// Redirect with success message
$message = sprintf(
    "Saved successfully! 4.2.1 Marks: %.2f/15 | 4.2.2 Marks: %.2f/5 | Total 4.2: %.2f/20",
    $marks_421,
    $marks_422,
    $total_marks_42
);

header("Location: ../../frontend/nba_page.php?criteria=" . urlencode('4.2 - Success Rate in the Stipulated Period of Program (20)') . "&msg=" . urlencode($message) . "&type=success");
exit;
