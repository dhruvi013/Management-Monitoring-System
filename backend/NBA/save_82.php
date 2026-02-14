<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$academic_year = trim($_POST['academic_year'] ?? '');
$x_phd = intval($_POST['x_phd'] ?? 0);
$y_mtech = intval($_POST['y_mtech'] ?? 0);
$rf_required = intval($_POST['rf_required'] ?? 0);

if ($academic_year === '' || $rf_required <= 0) {
    $msg = urlencode("Invalid Input. Required Faculty (RF) must be greater than 0.");
    header("Location: ../../frontend/nba_page.php?criteria=8.2&msg=$msg&type=error");
    exit;
}

// Assessment of Faculty Qualification (FQ)
// Formula: (5x + 3y) / RF
$assessment_score = ((5 * $x_phd) + (3 * $y_mtech)) / $rf_required;
$assessment_score = round($assessment_score, 2);

$id = $_POST['id'] ?? null;

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_82 SET academic_year = :year, x_phd = :x, y_mtech = :y, rf_required = :rf, assessment_score = :score WHERE id = :id");
        $stmt->execute([':year' => $academic_year, ':x' => $x_phd, ':y' => $y_mtech, ':rf' => $rf_required, ':score' => $assessment_score, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_82 (academic_year, x_phd, y_mtech, rf_required, assessment_score) VALUES (:year, :x, :y, :rf, :score)");
        $stmt->execute([':year' => $academic_year, ':x' => $x_phd, ':y' => $y_mtech, ':rf' => $rf_required, ':score' => $assessment_score]);
    }
    $msg = urlencode("Saved successfully! Assessment Score: $assessment_score");
    header("Location: ../../frontend/nba_page.php?criteria=8.2&msg=$msg&type=success");
} catch (PDOException $e) {
    $msg = urlencode("DB Error: " . $e->getMessage());
    header("Location: ../../frontend/nba_page.php?criteria=8.2&msg=$msg&type=error");
}
?>
