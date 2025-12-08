<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Get form data
$academic_year = trim($_POST['academic_year'] ?? '');
$magazine = trim($_POST['magazine'] ?? 'No');
$target_freq1 = intval($_POST['target_freq1'] ?? 0);
$newsletter = trim($_POST['newsletter'] ?? 'No');
$target_freq2 = intval($_POST['target_freq2'] ?? 0);

// Validate input
if ($academic_year === '') {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.2 - Publications (Magazine/Newsletter) (5)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// ============================================
// Marking Logic (Total 5 marks)
// ============================================
// Full marks (5) if:
// - target_freq1 = 1 (magazine frequency)
// - target_freq2 = 4 (newsletter frequency)

$marks = 0;

// Check if targets are met
if ($target_freq1 >= 1 && $target_freq2 >= 4) {
    $marks = 5;
} else {
    // Proportional marking if targets not fully met
    $magazine_score = ($target_freq1 / 1) * 2.5; // Magazine contributes 2.5 marks
    $newsletter_score = ($target_freq2 / 4) * 2.5; // Newsletter contributes 2.5 marks
    
    $marks = min($magazine_score + $newsletter_score, 5);
}

// Insert into database
$stmt = $pdo->prepare("INSERT INTO nba_publications_452 (
    academic_year, magazine, target_freq1, newsletter, target_freq2, marks
) VALUES (
    :year, :magazine, :freq1, :newsletter, :freq2, :marks
)");

$stmt->execute([
    ':year' => $academic_year,
    ':magazine' => $magazine,
    ':freq1' => $target_freq1,
    ':newsletter' => $newsletter,
    ':freq2' => $target_freq2,
    ':marks' => $marks
]);

// Redirect with success message
$message = sprintf(
    "Saved successfully! Magazine Freq: %d | Newsletter Freq: %d | Marks: %.2f/5",
    $target_freq1,
    $target_freq2,
    $marks
);

header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.2 - Publications (Magazine/Newsletter) (5)') . "&msg=" . urlencode($message) . "&type=success");
exit;
