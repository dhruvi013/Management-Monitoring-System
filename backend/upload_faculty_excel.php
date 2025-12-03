<?php
// backend/upload_file.php
require_once __DIR__ . '/db.php';

function read_xlsx_as_csv_array($filepath) {
    $zip = new ZipArchive;
    if ($zip->open($filepath) !== TRUE) return [];

    $shared = [];
    if (($xml = $zip->getFromName("xl/sharedStrings.xml"))) {
        $sx = simplexml_load_string($xml);
        foreach ($sx->si as $s) $shared[] = (string)$s->t;
    }

    $xml = $zip->getFromName("xl/worksheets/sheet1.xml");
    if (!$xml) return [];

    $sheet = simplexml_load_string($xml);
    $rows = [];
    foreach ($sheet->sheetData->row as $row) {
        $r = [];
        foreach ($row->c as $c) {
            $value = (string)$c->v;
            if (isset($c['t']) && $c['t'] == 's') $value = $shared[(int)$value] ?? '';
            $r[] = $value;
        }
        $rows[] = $r;
    }
    $zip->close();
    return $rows;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    header('Location: ../frontend/index.php?msg=' . urlencode("Invalid request") . "&type=error");
    exit;
}

$batch = trim($_POST['batch'] ?? '');
$academic_year = trim($_POST['academic_year'] ?? '');
if ($batch === '' || $academic_year === '') {
    header("Location: ../frontend/index.php?msg=" . urlencode("Batch & Academic Year required") . "&type=error");
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../frontend/index.php?msg=" . urlencode("Upload failed") . "&type=error");
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$tmp = $file['tmp_name'];

$data = [];
if ($ext === 'csv') {
    $data = array_map('str_getcsv', file($tmp));
} elseif ($ext === 'xlsx') {
    $data = read_xlsx_as_csv_array($tmp);
} else {
    header("Location: ../frontend/index.php?msg=" . urlencode("Only CSV or XLSX allowed") . "&type=error");
    exit;
}

if (!$data || count($data) < 2) {
    header("Location: ../frontend/index.php?msg=" . urlencode("File is empty or invalid") . "&type=error");
    exit;
}

// Process header row
$headers = array_map('strtolower', array_map('trim', $data[0]));
$required = ['gr no', 'roll no', 'student name'];
foreach ($required as $col) {
    if (!in_array($col, $headers)) {
        header("Location: ../frontend/index.php?msg=" . urlencode("Missing column: $col") . "&type=error");
        exit;
    }
}

// map header → index
$map = [];
foreach ($headers as $i => $h) $map[$h] = $i;

// Insert into DB
$pdo->beginTransaction();
$rowsInserted = 0;

try {
    $insert = $pdo->prepare("
        INSERT INTO students 
            (gr_no, enrollment_no, class, semester, first_name, middle_name, last_name, batch, academic_year)
        VALUES 
            (:gr_no, :enrollment_no, :class, :semester, :first_name, :middle_name, :last_name, :batch, :academic_year)
        ON DUPLICATE KEY UPDATE
            class = VALUES(class),
            semester = VALUES(semester),
            first_name = VALUES(first_name),
            middle_name = VALUES(middle_name),
            last_name = VALUES(last_name),
            batch = VALUES(batch),
            academic_year = VALUES(academic_year)
    ");

    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];
        if (!isset($row[$map['gr no']])) continue;

        $gr_no         = trim($row[$map['gr no']] ?? '');
        $enrollment_no = trim($row[$map['roll no']] ?? '');
        $full_name     = trim($row[$map['student name']] ?? '');

        if ($gr_no === '' || $enrollment_no === '' || $full_name === '') continue;

        // Split name
        $parts = preg_split('/\s+/', $full_name);
        $first = $parts[0];
        $middle = $parts[1] ?? null;
        $last = $parts[2] ?? null;

        // Default class and semester
        $class = 'N/A';
        $semester = 1;

        $insert->execute([
            ':gr_no' => $gr_no,
            ':enrollment_no' => $enrollment_no,
            ':class' => $class,
            ':semester' => $semester,
            ':first_name' => $first,
            ':middle_name' => $middle,
            ':last_name' => $last,
            ':batch' => $batch,
            ':academic_year' => $academic_year
        ]);

        $rowsInserted++;
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../frontend/index.php?msg=" . urlencode("DB Error: " . $e->getMessage()) . "&type=error");
    exit;
}

header("Location: ../frontend/index.php?msg=" . urlencode("Uploaded successfully: $rowsInserted rows") . "&type=success");
exit;
?>
