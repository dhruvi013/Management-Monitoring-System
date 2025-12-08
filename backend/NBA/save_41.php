<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$intake = intval($_POST['intake'] ?? 0);
$admitted = intval($_POST['admitted'] ?? 0);
$year = trim($_POST['year'] ?? '');

if ($intake <= 0 || $admitted < 0 || $year === '') {
    header("Location: ../frontend/nba_page.php?criteria=" . urlencode('4.1 - Enrollment Ratio (20)') . "&msg=" . urlencode("Invalid data") . "&type=error");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO nba_enrollment_41 (intake, admitted, academic_year) VALUES (:intake, :admitted, :year)");
$stmt->execute([':intake'=>$intake, ':admitted'=>$admitted, ':year'=>$year]);

header("Location: ../frontend/nba_page.php?criteria=" . urlencode('4.1 - Enrollment Ratio (20)') . "&msg=" . urlencode("Saved successfully") . "&type=success");
exit;
