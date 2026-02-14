<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$scope_details = trim($_POST['scope_details'] ?? '');
$facilities_materials = trim($_POST['facilities_materials'] ?? '');
$utilization_details = trim($_POST['utilization_details'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_94 SET academic_year = :year, scope_details = :sd, facilities_materials = :fm, utilization_details = :ud WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':sd' => $scope_details, ':fm' => $facilities_materials, ':ud' => $utilization_details, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_94 (academic_year, scope_details, facilities_materials, utilization_details) VALUES (:year, :sd, :fm, :ud)");
        $stmt->execute([':year' => $academic_year, ':sd' => $scope_details, ':fm' => $facilities_materials, ':ud' => $utilization_details]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=9.4&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=9.4&msg=$msg&type=error");
}
?>
