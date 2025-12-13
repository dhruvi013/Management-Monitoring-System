<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Get form data
$academic_year = trim($_POST['academic_year'] ?? '');
$no_of_chapters = intval($_POST['no_of_chapters'] ?? 0);
$international_events = intval($_POST['international_events'] ?? 0);
$national_events = intval($_POST['national_events'] ?? 0);
$state_events = intval($_POST['state_events'] ?? 0);
$dept_events = intval($_POST['dept_events'] ?? 0);

// Validate input
if ($academic_year === '') {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.1 - Professional Chapters and Events (5)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// ============================================
// Part A: Chapters Marking (Max 3 marks)
// ============================================
// 1 chapter = 1 mark
// 2 chapters = 2 marks
// 3+ chapters = 3 marks
$marks_a = min($no_of_chapters, 3);

// ============================================
// Part B: Events Marking (Max 2 marks)
// ============================================
// International: 1 event = 0.5 marks
// National: 1 event = 1 mark
// State: 5 events = 1 mark
// Department: 20+ events = 3 marks (but max for B is 2)

$marks_b = 0;

// International events
$marks_b += $international_events * 0.5;

// National events  
$marks_b += $national_events * 1;

// State events (5 events = 1 mark)
$marks_b += ($state_events / 5);

// Department events (20+ = 3 marks)
if ($dept_events >= 20) {
    $marks_b += 3;
} else {
    $marks_b += ($dept_events / 20) * 3;
}

// Cap marks_b at 2 (since Part B is max 2 marks out of total 5)
$marks_b = min($marks_b, 2);

// Total marks for 4.5.1 (max 5)
$total_marks = min($marks_a + $marks_b, 5);

// Insert into database
// Check/Insert/Update
if (!empty($_POST['id'])) {
    $stmt = $pdo->prepare("UPDATE nba_professional_451 SET 
        academic_year=:year, no_of_chapters=:chapters, international_events=:intl,
        national_events=:national, state_events=:state, dept_events=:dept,
        marks_a=:marks_a, marks_b=:marks_b, total_marks=:total
        WHERE id=:id");

    $stmt->execute([
        ':year' => $academic_year,
        ':chapters' => $no_of_chapters,
        ':intl' => $international_events,
        ':national' => $national_events,
        ':state' => $state_events,
        ':dept' => $dept_events,
        ':marks_a' => $marks_a,
        ':marks_b' => $marks_b,
        ':total' => $total_marks,
        ':id' => $_POST['id']
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO nba_professional_451 (
        academic_year, no_of_chapters, international_events, national_events,
        state_events, dept_events, marks_a, marks_b, total_marks
    ) VALUES (
        :year, :chapters, :intl, :national,
        :state, :dept, :marks_a, :marks_b, :total
    )");
    
    $stmt->execute([
        ':year' => $academic_year,
        ':chapters' => $no_of_chapters,
        ':intl' => $international_events,
        ':national' => $national_events,
        ':state' => $state_events,
        ':dept' => $dept_events,
        ':marks_a' => $marks_a,
        ':marks_b' => $marks_b,
        ':total' => $total_marks
    ]);
}

// Redirect with success message
$message = sprintf(
    "Saved successfully! Part A (Chapters): %.2f/3 | Part B (Events): %.2f/2 | Total: %.2f/5",
    $marks_a,
    $marks_b,
    $total_marks
);

header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.1 - Professional Chapters and Events (5)') . "&msg=" . urlencode($message) . "&type=success");
exit;
