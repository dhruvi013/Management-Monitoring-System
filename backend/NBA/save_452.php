<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Get form data
$academic_year = trim($_POST['academic_year'] ?? '');
$magazine = trim($_POST['magazine'] ?? 'No');
$target_freq1 = intval($_POST['target_freq1'] ?? 0); // expected target (e.g., 1)
$num_magazine_post = intval($_POST['num_magazine'] ?? 0); // user input when magazine = No

$newsletter = trim($_POST['newsletter'] ?? 'No');
$target_freq2 = intval($_POST['target_freq2'] ?? 0); // expected target (e.g., 4)
$num_newsletter_post = intval($_POST['num_newsletter'] ?? 0); // user input when newsletter = No

// Validate input
if ($academic_year === '') {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.2 - Publications (Magazine/Newsletter) (5)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// ============================================
//  MARKING LOGIC (TOTAL 5 MARKS)
//  Magazine → 2.5 Marks
//  Newsletter → 2.5 Marks
//  If Yes => num = freq (target); If No => use entered num
//  Score = min(num / target, 1) * 2.5  (safe for target = 0)
// ============================================

// Determine actual numbers to use for calculation
if ($magazine === "Yes") {
    // when Yes, number of magazines = specified frequency (target)
    $num_magazine = $target_freq1;
} else {
    // when No, use user entered count (expected < target)
    $num_magazine = max(0, $num_magazine_post);
}

if ($newsletter === "Yes") {
    $num_newsletter = $target_freq2;
} else {
    $num_newsletter = max(0, $num_newsletter_post);
}

// Calculate magazine score (2.5 marks total)
if ($target_freq1 > 0) {
    $ratio_mag = $num_magazine / $target_freq1;
    if ($ratio_mag < 0) $ratio_mag = 0;
    $ratio_mag = min($ratio_mag, 1); // cap at 1
    $magazine_score = $ratio_mag * 2.5;
} else {
    // If target is zero/invalid, give full marks if any magazine exists
    $magazine_score = ($num_magazine > 0) ? 2.5 : 0;
}

// Calculate newsletter score (2.5 marks total)
if ($target_freq2 > 0) {
    $ratio_news = $num_newsletter / $target_freq2;
    if ($ratio_news < 0) $ratio_news = 0;
    $ratio_news = min($ratio_news, 1); // cap at 1
    $newsletter_score = $ratio_news * 2.5;
} else {
    $newsletter_score = ($num_newsletter > 0) ? 2.5 : 0;
}

// Final marks (max 5)
$marks = $magazine_score + $newsletter_score;
if ($marks > 5) $marks = 5;

// Insert into DB (make sure table has num_magazine and num_newsletter columns)
// Check/Insert/Update
if (!empty($_POST['id'])) {
    $stmt = $pdo->prepare("UPDATE nba_publications_452 SET 
        academic_year=:year, magazine=:mag, target_freq1=:freq1, num_magazine=:num1,
        newsletter=:news, target_freq2=:freq2, num_newsletter=:num2, marks=:marks
        WHERE id=:id");
        
    $stmt->execute([
        ':year' => $academic_year,
        ':mag' => $magazine,
        ':freq1' => $target_freq1,
        ':num1' => $num_magazine,
        ':news' => $newsletter,
        ':freq2' => $target_freq2,
        ':num2' => $num_newsletter,
        ':marks' => $marks,
        ':id' => $_POST['id']
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO nba_publications_452 (
        academic_year, magazine, target_freq1, num_magazine,
        newsletter, target_freq2, num_newsletter, marks
    ) VALUES (
        :year, :mag, :freq1, :num1,
        :news, :freq2, :num2, :marks
    )");

    $stmt->execute([
        ':year' => $academic_year,
        ':mag' => $magazine,
        ':freq1' => $target_freq1,
        ':num1' => $num_magazine,
        ':news' => $newsletter,
        ':freq2' => $target_freq2,
        ':num2' => $num_newsletter,
        ':marks' => $marks
    ]);
}

// Redirect with message (show targets and used counts)
$message = sprintf(
    "Saved! Magazine: %s | Target: %d | Count used: %d | Newsletter: %s | Target: %d | Count used: %d | Marks: %.2f/5",
    $magazine, $target_freq1, $num_magazine,
    $newsletter, $target_freq2, $num_newsletter,
    $marks
);

header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.2 - Publications (Magazine/Newsletter) (5)') . "&msg=" . urlencode($message) . "&type=success");
exit;
