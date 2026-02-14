<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$mean_performance = floatval($_POST['mean_performance'] ?? 0); // Mean GPA * 10 OR Mean Percentage
$students_appeared = intval($_POST['students_appeared'] ?? 0);
$students_successful = intval($_POST['students_successful'] ?? 0);

if ($academic_year === '' || $students_appeared <= 0) {
    $msg = urlencode("Invalid Input. Students appeared must be greater than 0.");
    header("Location: ../../frontend/nba_page.php?criteria=8.3&msg=$msg&type=error");
    exit;
}

// API Calculation
// Formula: Academic Performance = Mean Performance * (Successful / Appeared)
$api_score = $mean_performance * ($students_successful / $students_appeared);
$api_score = round($api_score, 2);

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_83 SET academic_year = :year, mean_gpa_or_percentage = :mean, students_appeared = :app, students_successful = :succ, api_score = :score WHERE id = :id");
        $stmt->execute([
            ':year' => $academic_year, 
            ':mean' => $mean_performance, 
            ':app' => $students_appeared, 
            ':succ' => $students_successful, 
            ':score' => $api_score, 
            ':id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_83 (academic_year, mean_gpa_or_percentage, students_appeared, students_successful, api_score) VALUES (:year, :mean, :app, :succ, :score)");
        $stmt->execute([
            ':year' => $academic_year, 
            ':mean' => $mean_performance, 
            ':app' => $students_appeared, 
            ':succ' => $students_successful, 
            ':score' => $api_score
        ]);
    }
    $msg = urlencode("Saved successfully! API Score: $api_score");
    header("Location: ../../frontend/nba_page.php?criteria=8.3&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=8.3&msg=$msg&type=error");
}
?>
