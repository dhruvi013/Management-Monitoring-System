<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Create Table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_73 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        details TEXT NOT NULL,
        marks FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

$academic_year = trim($_POST['academic_year'] ?? '');
$details = trim($_POST['details'] ?? '');
$marks = floatval($_POST['marks'] ?? 0);

if ($academic_year === '') {
    $msg = urlencode("Academic Year is required.");
    header("Location: ../../frontend/nba_page.php?criteria=7.3&msg=$msg&type=error");
    exit;
}

$id = $_POST['id'] ?? null;

if ($id) {
    // Update
    $stmt = $pdo->prepare("UPDATE nba_criterion_73 SET academic_year = :year, details = :details, marks = :marks WHERE id = :id");
    $stmt->execute([
        ':year' => $academic_year,
        ':details' => $details,
        ':marks' => $marks,
        ':id' => $id
    ]);
} else {
    // Insert
    $stmt = $pdo->prepare("INSERT INTO nba_criterion_73 (academic_year, details, marks) VALUES (:year, :details, :marks)");
    $stmt->execute([
        ':year' => $academic_year,
        ':details' => $details,
        ':marks' => $marks
    ]);
}

$msg = urlencode("Saved successfully!");
header("Location: ../../frontend/nba_page.php?criteria=7.3&msg=$msg&type=success");
exit;
