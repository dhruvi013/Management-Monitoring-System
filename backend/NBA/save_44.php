<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Get form data
$academic_year = trim($_POST['academic_year'] ?? '');
$final_year_total = intval($_POST['final_year_total'] ?? 0);
$placed = intval($_POST['placed'] ?? 0);
$higher_studies = intval($_POST['higher_studies'] ?? 0);
$entrepreneur = intval($_POST['entrepreneur'] ?? 0);

// Validate input
if ($academic_year === '' || $final_year_total <= 0) {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.4 - Placement and Career Progression (30)') . "&msg=" . urlencode("Invalid data - Final year total must be greater than 0") . "&type=error");
    exit;
}

// Validate that individual counts don't exceed total
if (($placed + $higher_studies + $entrepreneur) > $final_year_total) {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.4 - Placement and Career Progression (30)') . "&msg=" . urlencode("Sum of placed, higher studies, and entrepreneurs cannot exceed total final year students") . "&type=error");
    exit;
}

// ============================================
// Calculate Assessment Index for this year
// ============================================
// Assessment Index = (x + y + z) / N
// where:
// x = Number of students placed
// y = Number of students in higher studies
// z = Number of students turned entrepreneur
// N = Total number of final year students

$assessment_index = ($placed + $higher_studies + $entrepreneur) / $final_year_total;

// Insert current year data
$stmt = $pdo->prepare("INSERT INTO nba_placement_44 (
    academic_year, final_year_total, placed, higher_studies, entrepreneur,
    assessment_index, marks
) VALUES (
    :year, :total, :placed, :higher, :entrepreneur,
    :index, 0
)");

$stmt->execute([
    ':year' => $academic_year,
    ':total' => $final_year_total,
    ':placed' => $placed,
    ':higher' => $higher_studies,
    ':entrepreneur' => $entrepreneur,
    ':index' => $assessment_index
]);

// ============================================
// Get last 3 years of data to calculate average
// ============================================
$stmt = $pdo->query("SELECT assessment_index FROM nba_placement_44 ORDER BY created_at DESC LIMIT 3");
$indices = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Calculate average assessment index
$avg_index = 0;
if (count($indices) > 0) {
    $avg_index = array_sum($indices) / count($indices);
}

// ============================================
// Calculate marks: 30 × Average Assessment Index
// ============================================
$marks = 30 * $avg_index;

// Update marks for all entries (since it's based on 3-year average)
$pdo->prepare("UPDATE nba_placement_44 SET marks = :marks")->execute([':marks' => $marks]);

// Redirect with success message
$message = sprintf(
    "Saved successfully! Assessment Index: %.4f | Marks: %.2f/30 (Based on %d year(s) average)",
    $assessment_index,
    $marks,
    count($indices)
);

header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.4 - Placement and Career Progression (30)') . "&msg=" . urlencode($message) . "&type=success");
exit;
