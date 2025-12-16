<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// ---------------------------------------------------------
// 1. Create Table if Not Exists (Safety Check)
// ---------------------------------------------------------
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_51 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        num_students INT NOT NULL,
        num_faculty INT NOT NULL,
        sfr FLOAT NOT NULL,
        avg_sfr FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// ---------------------------------------------------------
// 2. Process Input
// ---------------------------------------------------------
$academic_year = trim($_POST['academic_year'] ?? '');
$num_students = intval($_POST['num_students'] ?? 0);
$num_faculty = intval($_POST['num_faculty'] ?? 0);

if ($academic_year === '' || $num_students < 0 || $num_faculty <= 0) {
    // Basic validation failed
    $msg = urlencode("Invalid data. Ensure Faculty > 0 and Students >= 0.");
    header("Location: ../../frontend/nba_page.php?criteria=5.1&msg=$msg&type=error");
    exit;
}

// ---------------------------------------------------------
// 3. Calculate SFR & Average SFR
// ---------------------------------------------------------
$sfr = round($num_students / $num_faculty, 2);

// Calculate Average SFR from last 2 records + current
// Note: This logic assumes 'academic_year' string sorting or id-based order roughly correlates to time
// For a robust implementation, we might parsing year, but ID-based previous fetch is simpler for now
// or simply fetching all past records.
// Let's fetch the last 2 entered records (excluding current update/insert target ideally, but simpler to just fetch top 2 desc)

$id = $_POST['id'] ?? null;

// Fetch previous 2 SFRs
$sql_prev = "SELECT sfr FROM nba_criterion_51 WHERE id != :id ORDER BY academic_year DESC LIMIT 2";
$stmt_prev = $pdo->prepare($sql_prev);
$stmt_prev->execute([':id' => $id ?: 0]);
$prev_sfrs = $stmt_prev->fetchAll(PDO::FETCH_COLUMN);

$total_sfr = $sfr;
$count = 1;

foreach ($prev_sfrs as $p_sfr) {
    $total_sfr += $p_sfr;
    $count++;
}

$avg_sfr = round($total_sfr / $count, 2);


// ---------------------------------------------------------
// 4. Insert or Update
// ---------------------------------------------------------
if ($id) {
    // Update
    $stmt = $pdo->prepare("UPDATE nba_criterion_51 SET academic_year = :year, num_students = :stu, num_faculty = :fac, sfr = :sfr, avg_sfr = :avg WHERE id = :id");
    $stmt->execute([
        ':year' => $academic_year,
        ':stu' => $num_students,
        ':fac' => $num_faculty,
        ':sfr' => $sfr,
        ':avg' => $avg_sfr,
        ':id' => $id
    ]);
} else {
    // Insert
    $stmt = $pdo->prepare("INSERT INTO nba_criterion_51 (academic_year, num_students, num_faculty, sfr, avg_sfr) VALUES (:year, :stu, :fac, :sfr, :avg)");
    $stmt->execute([
        ':year' => $academic_year,
        ':stu' => $num_students,
        ':fac' => $num_faculty,
        ':sfr' => $sfr,
        ':avg' => $avg_sfr
    ]);
}

// ---------------------------------------------------------
// 5. Calculate Average SFR (Last 3 Years) for notification
// ---------------------------------------------------------
$stmt = $pdo->query("SELECT sfr FROM nba_criterion_51 ORDER BY academic_year DESC LIMIT 3");
$ratios = $stmt->fetchAll(PDO::FETCH_COLUMN);

$avg_sfr = 0;
if (count($ratios) > 0) {
    $avg_sfr = array_sum($ratios) / count($ratios);
}

// ---------------------------------------------------------
// 6. Redirect
// ---------------------------------------------------------
$msg = urlencode("Saved successfully! SFR: $sfr. Avg SFR (Last 3 Years): " . number_format($avg_sfr, 2));
header("Location: ../../frontend/nba_page.php?criteria=5.1&msg=$msg&type=success");
exit;
