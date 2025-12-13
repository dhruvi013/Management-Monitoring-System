<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$intake = intval($_POST['intake'] ?? 0);
$admitted = intval($_POST['admitted'] ?? 0);
$academic_year = trim($_POST['academic_year'] ?? '');

if ($intake <= 0 || $admitted < 0 || $academic_year === '') {
    header("Location: ../../frontend/nba_page.php?criteria=" . urlencode('4.1 - Enrollment Ratio (20)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// Calculate enrollment ratio for this year
$enrollment_ratio = ($admitted / $intake) * 100;

// Check if ID exists for update
$id = $_POST['id'] ?? null;

if ($id) {
    // Update existing record
    $stmt = $pdo->prepare("UPDATE nba_enrollment_41 SET academic_year = :year, intake = :intake, admitted = :admitted, enrollment_ratio = :ratio WHERE id = :id");
    $stmt->execute([
        ':year' => $academic_year, 
        ':intake' => $intake, 
        ':admitted' => $admitted, 
        ':ratio' => $enrollment_ratio,
        ':id' => $id
    ]);
} else {
    // Insert new record
    $stmt = $pdo->prepare("INSERT INTO nba_enrollment_41 (academic_year, intake, admitted, enrollment_ratio, marks) VALUES (:year, :intake, :admitted, :ratio, 0)");
    $stmt->execute([
        ':year' => $academic_year, 
        ':intake' => $intake, 
        ':admitted' => $admitted, 
        ':ratio' => $enrollment_ratio
    ]);
}

// Get last 3 years of data to calculate average
$stmt = $pdo->query("SELECT enrollment_ratio FROM nba_enrollment_41 ORDER BY created_at DESC LIMIT 3");
$ratios = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Calculate average enrollment ratio
$avg_ratio = 0;
if (count($ratios) > 0) {
    $avg_ratio = array_sum($ratios) / count($ratios);
}

// Apply marking scheme based on average
// A. ≥90% → 20 marks
// B. ≥80% → 18 marks  
// C. ≥70% → 16 marks
// D. ≥60% → 14 marks
// Otherwise → 0 marks
$marks = 0;
if ($avg_ratio >= 90) {
    $marks = 20;
} elseif ($avg_ratio >= 80) {
    $marks = 18;
} elseif ($avg_ratio >= 70) {
    $marks = 16;
} elseif ($avg_ratio >= 60) {
    $marks = 14;
}

// Update marks for all entries (since it's based on 3-year average)
$pdo->prepare("UPDATE nba_enrollment_41 SET marks = :marks")->execute([':marks' => $marks]);

header("Location: ../../frontend/nba_page.php?criteria=" . urlencode('4.1 - Enrollment Ratio (20)') . "&msg=" . urlencode("Saved successfully! Marks: $marks/20 (Avg Ratio: " . number_format($avg_ratio, 2) . "%)") . "&type=success");
exit;
