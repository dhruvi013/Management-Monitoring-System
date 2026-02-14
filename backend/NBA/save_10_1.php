<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$strategic_plan = trim($_POST['strategic_plan'] ?? '');
$admin_setup = trim($_POST['admin_setup'] ?? '');
$decentralization = trim($_POST['decentralization'] ?? '');
$faculty_participation = trim($_POST['faculty_participation'] ?? '');

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_10_1 SET academic_year = :year, strategic_plan = :sp, admin_setup = :as, decentralization = :dc, faculty_participation = :fp WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':sp' => $strategic_plan, ':as' => $admin_setup, ':dc' => $decentralization, ':fp' => $faculty_participation, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_10_1 (academic_year, strategic_plan, admin_setup, decentralization, faculty_participation) VALUES (:year, :sp, :as, :dc, :fp)");
        $stmt->execute([':year' => $academic_year, ':sp' => $strategic_plan, ':as' => $admin_setup, ':dc' => $decentralization, ':fp' => $faculty_participation]);
    }
    $msg = urlencode("Saved successfully!");
    header("Location: ../../frontend/nba_page.php?criteria=10.1&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=10.1&msg=$msg&type=error");
}
?>
