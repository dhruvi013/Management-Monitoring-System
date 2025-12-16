<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Create Table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_53 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        x_phd INT NOT NULL,
        y_mtech INT NOT NULL,
        f_required INT NOT NULL,
        fq_score FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

$academic_year = trim($_POST['academic_year'] ?? '');
$x_phd = intval($_POST['x_phd'] ?? 0);
$y_mtech = intval($_POST['y_mtech'] ?? 0);
$f_required = intval($_POST['f_required'] ?? 0);

if ($academic_year === '' || $x_phd < 0 || $y_mtech < 0 || $f_required <= 0) {
    $msg = urlencode("Invalid data. Ensure F > 0 and others >= 0.");
    header("Location: ../../frontend/nba_page.php?criteria=5.3&msg=$msg&type=error");
    exit;
}

// Calculate FQ
// Formula: FQ = 2.0 * [(10X + 4Y) / F]
$fq_score = 0;
if ($f_required > 0) {
    $fq_score = 2.0 * ((10 * $x_phd + 4 * $y_mtech) / $f_required);
    $fq_score = round($fq_score, 2);
}

$id = $_POST['id'] ?? null;

if ($id) {
    // Update
    $stmt = $pdo->prepare("UPDATE nba_criterion_53 SET academic_year = :year, x_phd = :x, y_mtech = :y, f_required = :f, fq_score = :fq WHERE id = :id");
    $stmt->execute([
        ':year' => $academic_year,
        ':x' => $x_phd,
        ':y' => $y_mtech,
        ':f' => $f_required,
        ':fq' => $fq_score,
        ':id' => $id
    ]);
} else {
    // Insert
    $stmt = $pdo->prepare("INSERT INTO nba_criterion_53 (academic_year, x_phd, y_mtech, f_required, fq_score) VALUES (:year, :x, :y, :f, :fq)");
    $stmt->execute([
        ':year' => $academic_year,
        ':x' => $x_phd,
        ':y' => $y_mtech,
        ':f' => $f_required,
        ':fq' => $fq_score
    ]);
}

$msg = urlencode("Saved successfully! FQ Score: $fq_score");
header("Location: ../../frontend/nba_page.php?criteria=5.3&msg=$msg&type=success");
exit;
