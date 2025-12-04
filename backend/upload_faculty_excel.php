<?php
// backend/upload_file.php
require_once __DIR__ . '/db.php';

/*
------------------------------------------------------
 XLSX READER (CORRECTED VERSION — READS TRUE COLUMN POSITIONS)
------------------------------------------------------
*/
function read_xlsx_as_csv_array($filepath) {
    $zip = new ZipArchive;
    if ($zip->open($filepath) !== TRUE) return [];

    // Read shared strings
    $shared = [];
    if (($xml = $zip->getFromName("xl/sharedStrings.xml"))) {
        $sx = simplexml_load_string($xml);
        foreach ($sx->si as $s) {
            $shared[] = (string)$s->t;
        }
    }

    // Read sheet1
    $xml = $zip->getFromName("xl/worksheets/sheet1.xml");
    if (!$xml) return [];

    $sheet = simplexml_load_string($xml);
    $rows = [];

    foreach ($sheet->sheetData->row as $row) {
        $r = [];

        foreach ($row->c as $c) {
            // Example: A1, C1, D1
            $cellRef = (string)$c['r'];

            // Extract column letters (A, B, C)
            preg_match('/([A-Z]+)/', $cellRef, $m);
            $col = $m[1];

            // Convert A, B, C ... AA into column index
            $index = 0;
            $len = strlen($col);
            for ($i = 0; $i < $len; $i++) {
                $index = $index * 26 + (ord($col[$i]) - ord('A') + 1);
            }
            $index--; // zero-based

            $value = (string)$c->v;

            // Shared string
            if (isset($c['t']) && $c['t'] == 's') {
                $value = $shared[(int)$value] ?? '';
            }

            $r[$index] = $value;
        }

        // Sort by column order
        ksort($r);

        // Convert to plain sequential array
        $rows[] = array_values($r);
    }

    $zip->close();
    return $rows;
}

/*
------------------------------------------------------
 VALIDATE REQUEST
------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    redirect("Invalid request", "error");
}

$academic_year = trim($_POST['academic_year'] ?? '');
if ($academic_year === '') {
    redirect("Academic Year is required", "error");
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    redirect("Upload failed", "error");
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$tmp = $file['tmp_name'];

/*
------------------------------------------------------
 READ FILE (CSV / XLSX)
------------------------------------------------------
*/
$data = [];

if ($ext === 'csv') {
    $data = array_map('str_getcsv', file($tmp));
} elseif ($ext === 'xlsx') {
    $data = read_xlsx_as_csv_array($tmp);
} else {
    redirect("Only CSV or XLSX allowed", "error");
}

if (!$data || count($data) < 2) {
    redirect("File is empty or invalid", "error");
}

/*
------------------------------------------------------
 VALIDATE HEADERS
------------------------------------------------------
*/
$headers = array_map('strtolower', array_map('trim', $data[0]));

$required = ['faculty id', 'faculty name', 'department', 'designation'];

foreach ($required as $col) {
    if (!in_array($col, $headers)) {
        redirect("Missing column: $col", "error");
    }
}

/*
------------------------------------------------------
 MAP HEADER INDEXES
------------------------------------------------------
*/
$map = [];
foreach ($headers as $i => $h) {
    $map[$h] = $i;
}

/*
------------------------------------------------------
 INSERT INTO DATABASE
------------------------------------------------------
*/
$pdo->beginTransaction();
$rowsInserted = 0;

try {
    $insert = $pdo->prepare("
        INSERT INTO faculty
            (faculty_id, first_name, middle_name, last_name, department, designation, academic_year)
        VALUES
            (:faculty_id, :first_name, :middle_name, :last_name, :department, :designation, :academic_year)
        ON DUPLICATE KEY UPDATE
            first_name = VALUES(first_name),
            middle_name = VALUES(middle_name),
            last_name = VALUES(last_name),
            department = VALUES(department),
            designation = VALUES(designation),
            academic_year = VALUES(academic_year)
    ");

    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];

        $faculty_id  = trim($row[$map['faculty id']] ?? '');
        $full_name   = trim($row[$map['faculty name']] ?? '');
        $department  = trim($row[$map['department']] ?? '');
        $designation = trim($row[$map['designation']] ?? '');

        if ($faculty_id === '' || $full_name === '') continue;

        // Split full name
        $parts = preg_split('/\s+/', $full_name);
        $first  = $parts[0] ?? '';
        $middle = $parts[1] ?? null;
        $last   = $parts[2] ?? null;

        $insert->execute([
            ':faculty_id'    => $faculty_id,
            ':first_name'    => $first,
            ':middle_name'   => $middle,
            ':last_name'     => $last,
            ':department'    => $department,
            ':designation'   => $designation,
            ':academic_year' => $academic_year
        ]);

        $rowsInserted++;
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    redirect("DB Error: " . $e->getMessage(), "error");
}

redirect("Uploaded successfully: $rowsInserted rows", "success");


function redirect($msg, $type) {
    header("Location: ../frontend/index.php?msg=" . urlencode($msg) . "&type=" . $type);
    exit;
}

?>
