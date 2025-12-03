<?php
require_once __DIR__ . '/db.php';

$action = $_POST['action'] ?? '';
$department = $_POST['department'] ?? '';

if ($action === 'no_transfer') {
    header("Location: ../frontend/index.php?msg=" . urlencode("No changes applied.") . "&type=success");
    exit;
}

if (!$department) {
    header("Location: ../frontend/index.php?msg=" . urlencode("Select a department.") . "&type=error");
    exit;
}

$stmt = $pdo->prepare("SELECT academic_year FROM faculty WHERE department = :d GROUP BY academic_year");
$stmt->execute([':d' => $department]);
$years = $stmt->fetchAll();

function nextAcademicYear($year) {
    list($s, $e) = explode('-', $year);
    $s++; $e++;
    $e = str_pad($e, 2, '0', STR_PAD_LEFT);
    return $s . '-' . $e;
}

$pdo->beginTransaction();

try {
    foreach ($years as $y) {
        $old = $y['academic_year'];
        $new = nextAcademicYear($old);

        $up = $pdo->prepare("
            UPDATE faculty 
            SET academic_year = :new 
            WHERE department = :d AND academic_year = :old
        ");

        $up->execute([':new' => $new, ':d' => $department, ':old' => $old]);
    }

    $pdo->commit();
    header("Location: ../frontend/index.php?msg=" . urlencode("Faculty transferred successfully") . "&type=success");

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../frontend/index.php?msg=" . urlencode("Error: " . $e->getMessage()) . "&type=error");
}
?>
