<?php
// backend/upload_csv.php
require_once __DIR__ . '/db.php';

// Only process POST file uploads
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) {
    header('Location: ../frontend/index.php');
    exit;
}

$file = $_FILES['csv_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../frontend/index.php?msg=" . urlencode("File upload error") . "&type=error");
    exit;
}

// Basic check on file type - accept csv or text
$allowed = ['text/csv', 'application/vnd.ms-excel', 'text/plain'];
// But do not rely solely on MIME
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
if (strtolower($ext) !== 'csv' && !in_array($file['type'], $allowed)) {
    // We still allow if extension csv, otherwise reject.
    // For XLSX, we recommend using PhpSpreadsheet (notes below).
}

// Move to a temp location
$tmpPath = $file['tmp_name'];

// Open and parse CSV
$handle = fopen($tmpPath, 'r');
if ($handle === false) {
    header("Location: ../frontend/index.php?msg=" . urlencode("Unable to read uploaded file") . "&type=error");
    exit;
}

// Read header row
$headers = fgetcsv($handle);
if ($headers === false) {
    header("Location: ../frontend/index.php?msg=" . urlencode("CSV is empty") . "&type=error");
    exit;
}

// Normalize headers
$normalized = array_map(function($h) {
    return strtolower(trim($h));
}, $headers);

// expected fields we support
$expected = ['first_name','middle_name','last_name','gr_no','enrollment_no','class','batch','academic_year'];

// find mapping from header name to column index
$map = [];
foreach ($normalized as $i => $h) {
    $map[$h] = $i;
}

// verify minimal required headers exist (at least these)
$required_headers = ['first_name','last_name','gr_no','enrollment_no','class','academic_year'];
foreach ($required_headers as $rh) {
    if (!array_key_exists($rh, $map)) {
        fclose($handle);
        header("Location: ../frontend/index.php?msg=" . urlencode("CSV missing required header: $rh") . "&type=error");
        exit;
    }
}

$rowsInserted = 0;
$pdo->beginTransaction();
try {
    $insertStmt = $pdo->prepare("INSERT INTO students (first_name, middle_name, last_name, gr_no, enrollment_no, class, batch, academic_year)
                               VALUES (:first_name, :middle_name, :last_name, :gr_no, :enrollment_no, :class, :batch, :academic_year)
                               ON DUPLICATE KEY UPDATE first_name=VALUES(first_name), middle_name=VALUES(middle_name), last_name=VALUES(last_name), class=VALUES(class), batch=VALUES(batch), academic_year=VALUES(academic_year)");

    while (($data = fgetcsv($handle)) !== false) {
        // read values safely by mapping index
        $get = function($field) use ($map, $data) {
            if (!isset($map[$field])) return null;
            $idx = $map[$field];
            return isset($data[$idx]) ? trim($data[$idx]) : null;
        };

        $first_name = $get('first_name');
        $middle_name = $get('middle_name');
        $last_name = $get('last_name');
        $gr_no = $get('gr_no');
        $enrollment_no = $get('enrollment_no');
        $class = $get('class');
        $batch = $get('batch');
        $academic_year = $get('academic_year');

        if (!$first_name || !$last_name || !$gr_no || !$enrollment_no || !$class || !$academic_year) {
            // skip invalid row - or collect errors if you want
            continue;
        }

        $insertStmt->execute([
            ':first_name' => $first_name,
            ':middle_name' => $middle_name,
            ':last_name' => $last_name,
            ':gr_no' => $gr_no,
            ':enrollment_no' => $enrollment_no,
            ':class' => $class,
            ':batch' => $batch,
            ':academic_year' => $academic_year
        ]);

        $rowsInserted++;
    }

    $pdo->commit();
    fclose($handle);
    header("Location: ../frontend/index.php?msg=" . urlencode("CSV processed. Rows added/updated: $rowsInserted") . "&type=success");
} catch (Exception $e) {
    $pdo->rollBack();
    fclose($handle);
    header("Location: ../frontend/index.php?msg=" . urlencode("CSV processing failed: " . $e->getMessage()) . "&type=error");
    exit;
}
