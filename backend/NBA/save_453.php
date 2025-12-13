<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Get form data
$academic_year = trim($_POST['academic_year'] ?? '');
$total_participation = intval($_POST['total_participation'] ?? 0);
$participation_within_state = intval($_POST['participation_within_state'] ?? 0);
$participation_outside_state = intval($_POST['participation_outside_state'] ?? 0);
$awards = intval($_POST['awards'] ?? 0);

// Validate input
if ($academic_year === '' || $total_participation <= 0) {
    header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.3 - Student Participation in Events (10)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

// Calculate percentages
$within_state_percentage = ($participation_within_state / $total_participation) * 100;
$outside_state_percentage = ($participation_outside_state / $total_participation) * 100;

// Check/Insert/Update
$id = $_POST['id'] ?? null;
if (!empty($id)) {
    // Update existing record
    $stmt = $pdo->prepare("UPDATE nba_participation_453 SET
        academic_year=:year, total_participation=:total,
        participation_within_state=:within, participation_outside_state=:outside,
        awards=:awards, within_state_percentage=:within_pct, 
        outside_state_percentage=:outside_pct
        WHERE id=:id");

    $stmt->execute([
        ':year' => $academic_year,
        ':total' => $total_participation,
        ':within' => $participation_within_state,
        ':outside' => $participation_outside_state,
        ':awards' => $awards,
        ':within_pct' => $within_state_percentage,
        ':outside_pct' => $outside_state_percentage,
        ':id' => $id
    ]);
} else {
    // Insert new record
    $stmt = $pdo->prepare("INSERT INTO nba_participation_453 (
        academic_year, total_participation, 
        participation_within_state, participation_outside_state,
        awards, within_state_percentage, outside_state_percentage, marks
    ) VALUES (
        :year, :total, :within, :outside, :awards, :within_pct, :outside_pct, 0
    )");

    $stmt->execute([
        ':year' => $academic_year,
        ':total' => $total_participation,
        ':within' => $participation_within_state,
        ':outside' => $participation_outside_state,
        ':awards' => $awards,
        ':within_pct' => $within_state_percentage,
        ':outside_pct' => $outside_state_percentage
    ]);
}

// ============================================
// Get last 4 years of data for calculation
// ============================================
$stmt = $pdo->query("SELECT * FROM nba_participation_453 ORDER BY created_at DESC LIMIT 4");
$last_4_years = $stmt->fetchAll();

if (count($last_4_years) > 0) {
    // Calculate average total participation over last 4 years
    $avg_total = 0;
    $avg_within = 0;
    $avg_outside = 0;
    $avg_awards = 0;
    
    foreach ($last_4_years as $year_data) {
        $avg_total += $year_data['total_participation'];
        $avg_within += $year_data['participation_within_state'];
        $avg_outside += $year_data['participation_outside_state'];
        $avg_awards += $year_data['awards'];
    }
    
    $count = count($last_4_years);
    $avg_total = $avg_total / $count;
    $avg_within = $avg_within / $count;
    $avg_outside = $avg_outside / $count;
    $avg_awards = $avg_awards / $count;
    
    // Calculate average percentages
    $avg_within_pct = ($avg_within / $avg_total) * 100;
    $avg_outside_pct = ($avg_outside / $avg_total) * 100;
    
    // ============================================
    // Marking Logic (Total 10 marks)
    // ============================================
    // Targets:
    // - Within state: 40% of total participation
    // - Outside state: 20% of total participation
    // - Awards: 5
    // Full marks if all targets met or exceeded
    
    $marks = 0;
    
    // Within state participation (4 marks for 40% target)
    $within_target = 40;
    if ($avg_within_pct >= $within_target) {
        $marks += 4;
    } else {
        $marks += ($avg_within_pct / $within_target) * 4;
    }
    
    // Outside state participation (4 marks for 20% target)
    $outside_target = 20;
    if ($avg_outside_pct >= $outside_target) {
        $marks += 4;
    } else {
        $marks += ($avg_outside_pct / $outside_target) * 4;
    }
    
    // Awards (2 marks for 5 awards target)
    $awards_target = 5;
    if ($avg_awards >= $awards_target) {
        $marks += 2;
    } else {
        $marks += ($avg_awards / $awards_target) * 2;
    }
    
    // Cap at 10 marks
    $marks = min($marks, 10);
    
    // Update marks for all entries
    $pdo->prepare("UPDATE nba_participation_453 SET marks = :marks")->execute([':marks' => $marks]);
    
    // Redirect with success message
    $message = sprintf(
        "Saved successfully! 4-Year Avg - Within: %.2f%% | Outside: %.2f%% | Awards: %.2f | Marks: %.2f/10",
        $avg_within_pct,
        $avg_outside_pct,
        $avg_awards,
        $marks
    );
} else {
    $message = "Data saved. Need at least 1 year of data to calculate marks.";
}

header("Location: ../../frontend/nba_page.php?subcriteria=" . urlencode('4.5.3 - Student Participation in Events (10)') . "&msg=" . urlencode($message) . "&type=success");
exit;
