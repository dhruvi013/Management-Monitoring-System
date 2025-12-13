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
$sem3_avg_sgpa = floatval($_POST['sem3_avg_sgpa'] ?? 0);
$sem4_avg_sgpa = floatval($_POST['sem4_avg_sgpa'] ?? 0);
$sem3_credit = intval($_POST['sem3_credit'] ?? 0);
$sem4_credit = intval($_POST['sem4_credit'] ?? 0);
$total_mean_cgpa = floatval($_POST['total_mean_cgpa'] ?? 0);
$success_2ndyear = intval($_POST['success_2ndyear'] ?? 0);
$students_appeared = intval($_POST['students_appeared'] ?? 0);

// Validate input
if ($academic_year === '' || $admitted_degree < 0 || $admitted_d2d < 0 || $students_appeared <= 0) {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.3 - Academic Performance in Second Year (10)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// Calculate total admitted
$total_admitted = $admitted_degree + $admitted_d2d;

// Recalculate mean CGPA to ensure accuracy
$total_credits = $sem3_credit + $sem4_credit;
if ($total_credits > 0) {
    $total_mean_cgpa = (($sem3_avg_sgpa * $sem3_credit) + ($sem4_avg_sgpa * $sem4_credit)) / $total_credits;
} else {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.3 - Academic Performance in Second Year (10)') . "&msg=" . urlencode("Total credits cannot be zero") . "&type=error");
    exit;
}

// Calculate success rate
$success_rate = ($success_2ndyear / $students_appeared) * 100;

// ============================================
// Calculate API (Academic Performance Index)
// ============================================
// API = (Mean CGPA / 10) × (Successful students / Students appeared)
// Since Mean CGPA is already on a 10-point scale, we use it directly
$api = ($total_mean_cgpa / 10) * ($success_2ndyear / $students_appeared);

// Calculate marks: 10 × API
$marks = 10 * $api;

// Insert into database
// Check/Insert/Update
if (!empty($_POST['id'])) {
    $stmt = $pdo->prepare("UPDATE nba_academic_43 SET 
        academic_year=:year, admitted_degree=:deg, admitted_d2d=:d2d, total_admitted=:total,
        sem3_avg_sgpa=:sem3_sgpa, sem4_avg_sgpa=:sem4_sgpa, sem3_credit=:sem3_credit, sem4_credit=:sem4_credit,
        total_mean_cgpa=:mean_cgpa, success_2ndyear=:success, students_appeared=:appeared,
        success_rate=:success_rate, api=:api 
        WHERE id=:id");
        
    $stmt->execute([
        ':year' => $academic_year,
        ':deg' => $admitted_degree,
        ':d2d' => $admitted_d2d,
        ':total' => $total_admitted,
        ':sem3_sgpa' => $sem3_avg_sgpa,
        ':sem4_sgpa' => $sem4_avg_sgpa,
        ':sem3_credit' => $sem3_credit,
        ':sem4_credit' => $sem4_credit,
        ':mean_cgpa' => $total_mean_cgpa,
        ':success' => $success_2ndyear,
        ':appeared' => $students_appeared,
        ':success_rate' => $success_rate,
        ':api' => $api,
        ':id' => $_POST['id']
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO nba_academic_43 (
        academic_year, admitted_degree, admitted_d2d, total_admitted,
        sem3_avg_sgpa, sem4_avg_sgpa, sem3_credit, sem4_credit,
        total_mean_cgpa, success_2ndyear, students_appeared,
        success_rate, api, marks
    ) VALUES (
        :year, :deg, :d2d, :total,
        :sem3_sgpa, :sem4_sgpa, :sem3_credit, :sem4_credit,
        :mean_cgpa, :success, :appeared,
        :success_rate, :api, :marks
    )");

    $stmt->execute([
        ':year' => $academic_year,
        ':deg' => $admitted_degree,
        ':d2d' => $admitted_d2d,
        ':total' => $total_admitted,
        ':sem3_sgpa' => $sem3_avg_sgpa,
        ':sem4_sgpa' => $sem4_avg_sgpa,
        ':sem3_credit' => $sem3_credit,
        ':sem4_credit' => $sem4_credit,
        ':mean_cgpa' => $total_mean_cgpa,
        ':success' => $success_2ndyear,
        ':appeared' => $students_appeared,
        ':success_rate' => $success_rate,
        ':api' => $api,
        ':marks' => $marks
    ]);
}

// Redirect with success message
$message = sprintf(
    "Saved successfully! API: %.4f | Marks: %.2f/10 | Mean CGPA: %.2f | Success Rate: %.2f%%",
    $api,
    $marks,
    $total_mean_cgpa,
    $success_rate
);

header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.3 - Academic Performance in Second Year (10)') . "&msg=" . urlencode($message) . "&type=success");
exit;
