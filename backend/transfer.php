<?php
require_once __DIR__ . '/db.php';

$batch = $_POST['batch'] ?? '';
$action = $_POST['action'] ?? '';

if (!$batch) {
    header("Location: ../frontend/index.php?msg=" . urlencode("Please select a batch") . "&type=error");
    exit;
}

if ($action === 'no_transfer') {
    header("Location: ../frontend/index.php?msg=" . urlencode("No changes applied.") . "&type=success");
    exit;
}

// Fetch current academic years for this batch
$stmt = $pdo->prepare("SELECT academic_year FROM students WHERE batch = :b GROUP BY academic_year");
$stmt->execute([':b' => $batch]);
$years = $stmt->fetchAll();

if (empty($years)) {
    header("Location: ../frontend/index.php?msg=" . urlencode("No students found in this batch") . "&type=error");
    exit;
}

/**
 * ALWAYS return proper academic year in YYYY-YY format
 * Example:
 * 2024-25 → 2025-26
 * 2025-26 → 2026-27
 */
function nextAcademicYear($year) {
    list($start, $end) = explode('-', $year);

    $start = intval($start) + 1;
    $end = intval($end) + 1;

    // ensure last part always stays 2 digits
    $end = str_pad($end, 2, '0', STR_PAD_LEFT);

    return $start . '-' . $end;
}

// Apply transfer
$pdo->beginTransaction();

try {
    foreach ($years as $y) {
        $oldYear = $y['academic_year'];
        $newYear = nextAcademicYear($oldYear);

        $update = $pdo->prepare("
            UPDATE students 
            SET academic_year = :new_year 
            WHERE batch = :b AND academic_year = :old_year
        ");

        $update->execute([
            ':new_year' => $newYear,
            ':b'        => $batch,
            ':old_year' => $oldYear
        ]);
    }

    $pdo->commit();

    header("Location: ../frontend/index.php?msg=" . urlencode("Batch transferred successfully.") . "&type=success");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../frontend/index.php?msg=" . urlencode("Error: " . $e->getMessage()) . "&type=error");
    exit;
}
