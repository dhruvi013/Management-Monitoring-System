<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

// Create Table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_52 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        req_prof INT NOT NULL,
        avail_prof INT NOT NULL,
        req_assoc INT NOT NULL,
        avail_assoc INT NOT NULL,
        req_asst INT NOT NULL,
        avail_asst INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

$academic_year = trim($_POST['academic_year'] ?? '');
$req_prof = intval($_POST['req_prof'] ?? 0);
$avail_prof = intval($_POST['avail_prof'] ?? 0);
$req_assoc = intval($_POST['req_assoc'] ?? 0);
$avail_assoc = intval($_POST['avail_assoc'] ?? 0);
$req_asst = intval($_POST['req_asst'] ?? 0);
$avail_asst = intval($_POST['avail_asst'] ?? 0);

if ($academic_year === '' || $req_prof < 0 || $avail_prof < 0) {
    $msg = urlencode("Invalid data. Please enter valid numbers.");
    header("Location: ../../frontend/nba_page.php?criteria=5.2&msg=$msg&type=error");
    exit;
}

// ---------------------------------------------------------
// 3. Calculate Averages & Marks (Logic from get_marks.php)
// ---------------------------------------------------------
$id = $_POST['id'] ?? null;

// Fetch previous 2 years data
// Exclude current ID if updating to avoid double counting or logic loop
// Logic: Fetch last 2 records by academic year descending
$sql_prev = "SELECT * FROM nba_criterion_52 WHERE id != :id ORDER BY academic_year DESC LIMIT 2";
$stmt_prev = $pdo->prepare($sql_prev);
$stmt_prev->execute([':id' => $id ?: 0]);
$history = $stmt_prev->fetchAll(PDO::FETCH_ASSOC);

// Add current data to history for calculation
$current_data = [
    'req_prof' => $req_prof, 'avail_prof' => $avail_prof,
    'req_assoc' => $req_assoc, 'avail_assoc' => $avail_assoc,
    'req_asst' => $req_asst, 'avail_asst' => $avail_asst
];
array_unshift($history, $current_data); // Add to beginning (or end, doesn't matter for sum)

$rf1 = 0; $af1 = 0; 
$rf2 = 0; $af2 = 0; 
$rf3 = 0; $af3 = 0; 
$count = count($history);

foreach($history as $h) {
    $rf1 += $h['req_prof']; $af1 += $h['avail_prof'];
    $rf2 += $h['req_assoc']; $af2 += $h['avail_assoc'];
    $rf3 += $h['req_asst']; $af3 += $h['avail_asst']; 
}

// Averages
$avg_rf1 = $count > 0 ? $rf1 / $count : 0;
$avg_af1 = $count > 0 ? $af1 / $count : 0;
$avg_rf2 = $count > 0 ? $rf2 / $count : 0;
$avg_af2 = $count > 0 ? $af2 / $count : 0;
$avg_rf3 = $count > 0 ? $rf3 / $count : 0;
$avg_af3 = $count > 0 ? $af3 / $count : 0;

// Ratios
$r1 = $avg_rf1 > 0 ? $avg_af1 / $avg_rf1 : 0;
$r2 = $avg_rf2 > 0 ? $avg_af2 / $avg_rf2 : 0;
$r3 = $avg_rf3 > 0 ? $avg_af3 / $avg_rf3 : 0;

// Marks
$cadre_marks = ($r1 + ($r2 * 0.6) + ($r3 * 0.4)) * 10;
// Note: Standard NBA often caps ratios at 1.0, but example implied otherwise. Keeping uncapped.

if ($id) {
    $stmt = $pdo->prepare("UPDATE nba_criterion_52 SET 
        academic_year = :year, 
        req_prof = :rp, avail_prof = :ap, 
        req_assoc = :ras, avail_assoc = :aas, 
        req_asst = :rast, avail_asst = :aast,
        avg_rf1 = :arf1, avg_af1 = :aaf1, ratio1 = :r1,
        avg_rf2 = :arf2, avg_af2 = :aaf2, ratio2 = :r2,
        avg_rf3 = :arf3, avg_af3 = :aaf3, ratio3 = :r3,
        marks = :marks
        WHERE id = :id");
    $stmt->execute([
        ':year' => $academic_year,
        ':rp' => $req_prof, ':ap' => $avail_prof,
        ':ras' => $req_assoc, ':aas' => $avail_assoc,
        ':rast' => $req_asst, ':aast' => $avail_asst,
        ':arf1' => $avg_rf1, ':aaf1' => $avg_af1, ':r1' => $r1,
        ':arf2' => $avg_rf2, ':aaf2' => $avg_af2, ':r2' => $r2,
        ':arf3' => $avg_rf3, ':aaf3' => $avg_af3, ':r3' => $r3,
        ':marks' => $cadre_marks,
        ':id' => $id
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO nba_criterion_52 
        (academic_year, req_prof, avail_prof, req_assoc, avail_assoc, req_asst, avail_asst,
         avg_rf1, avg_af1, ratio1, avg_rf2, avg_af2, ratio2, avg_rf3, avg_af3, ratio3, marks) 
        VALUES (:year, :rp, :ap, :ras, :aas, :rast, :aast,
         :arf1, :aaf1, :r1, :arf2, :aaf2, :r2, :arf3, :aaf3, :r3, :marks)");
    $stmt->execute([
        ':year' => $academic_year,
        ':rp' => $req_prof, ':ap' => $avail_prof,
        ':ras' => $req_assoc, ':aas' => $avail_assoc,
        ':rast' => $req_asst, ':aast' => $avail_asst,
        ':arf1' => $avg_rf1, ':aaf1' => $avg_af1, ':r1' => $r1,
        ':arf2' => $avg_rf2, ':aaf2' => $avg_af2, ':r2' => $r2,
        ':arf3' => $avg_rf3, ':aaf3' => $avg_af3, ':r3' => $r3,
        ':marks' => $cadre_marks
    ]);
}

$msg = urlencode("Saved successfully!");
header("Location: ../../frontend/nba_page.php?criteria=5.2&msg=$msg&type=success");
exit;
