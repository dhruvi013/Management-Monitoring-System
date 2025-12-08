<?php
// frontend/index.php
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/helpers.php';

// Detect active tab
$tab = $_GET['tab'] ?? 'students';

/* -------------------------
   FETCH STUDENTS
------------------------- */
$search = $_GET['search'] ?? '';
$params = [];
$where = '';

if ($search !== '') {
    $where = "WHERE first_name LIKE :s OR last_name LIKE :s OR gr_no LIKE :s OR enrollment_no LIKE :s";
    $params[':s'] = "%$search%";
}

$stmt = $pdo->prepare("SELECT * FROM students $where ORDER BY academic_year DESC, class, batch, last_name");
$stmt->execute($params);
$students = $stmt->fetchAll();

// Distinct batches
$batchStmt = $pdo->query("SELECT DISTINCT batch FROM students WHERE batch != '' ORDER BY batch");
$batches = $batchStmt->fetchAll();

/* -------------------------
   FETCH FACULTY
------------------------- */
$facultySearch = $_GET['faculty_search'] ?? '';
$fWhere = '';
$p = [];

if ($facultySearch !== '') {
    $fWhere = "WHERE first_name LIKE :s OR last_name LIKE :s OR faculty_id LIKE :s OR department LIKE :s";
    $p[':s'] = "%$facultySearch%";
}

$fq = $pdo->prepare("SELECT * FROM faculty $fWhere ORDER BY department, last_name");
$fq->execute($p);
$faculty = $fq->fetchAll();

// Distinct departments
$deptStmt = $pdo->query("SELECT DISTINCT department FROM faculty WHERE department != '' ORDER BY department");
$departments = $deptStmt->fetchAll();

$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Department Monitoring</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
<div class="max-w-7xl mx-auto">

  <header class="mb-6">
    <h1 class="text-3xl font-bold">Department Monitoring System</h1>
    <p class="text-sm text-gray-600">Manage Students & Faculty</p>
  </header>

  <!-- TABS -->
  <div class="flex gap-4 mb-6">

    <a href="?tab=students"
       class="px-4 py-2 rounded 
              <?= $tab === 'students' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' ?>">
      Students
    </a>

    <a href="?tab=faculty"
       class="px-4 py-2 rounded 
              <?= $tab === 'faculty' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' ?>">
      Faculty
    </a>

    <a href="?tab=nba"
       class="px-4 py-2 rounded 
              <?= $tab === 'nba' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' ?>">
      NBA
    </a>

  </div>

  

  <?php if ($msg): ?>
    <div class="<?= $type === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?> border px-4 py-3 rounded mb-4">
      <?= h($msg) ?>
    </div>
  <?php endif; ?>

  <!-- ============================
        STUDENT SECTION
  ============================ -->
  <?php if ($tab === 'students'): ?>
  
    <!-- Student Forms -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

      <!-- Add Student -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Add Student</h2>
        <form method="post" action="../backend/add_student.php" class="space-y-2">
          <div class="flex gap-2">
            <input required name="first_name" placeholder="First name" class="border p-2 w-1/3" />
            <input name="middle_name" placeholder="Middle name" class="border p-2 w-1/3" />
            <input required name="last_name" placeholder="Last name" class="border p-2 w-1/3" />
          </div>

          <div class="flex gap-2">
            <input required name="gr_no" placeholder="GR No" class="border p-2 w-1/2" />
            <input required name="enrollment_no" placeholder="Enrollment No" class="border p-2 w-1/2" />
          </div>

          <div class="flex gap-2">
            <input required name="class" placeholder="Class" class="border p-2 w-1/2" />
            <input required type="number" name="semester" placeholder="Semester" class="border p-2 w-1/2" />
          </div>

          <input required name="batch" placeholder="Batch" class="border p-2 w-full" />
          <input required name="academic_year" placeholder="Academic Year (2023-24)" class="border p-2 w-full" />

          <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Add Student</button>
        </form>
      </div>

      <!-- Bulk Upload -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Bulk Upload (Excel/CSV)</h2>
        <form method="post" action="../backend/upload_file.php" enctype="multipart/form-data" class="space-y-2">
          <input type="text" name="batch" required placeholder="Batch" class="border p-2 w-full" />
          <input type="text" name="academic_year" required placeholder="Academic Year" class="border p-2 w-full" />
          <input type="file" name="file" accept=".csv,.xlsx,.xls" required>
          <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Upload</button>
        </form>
      </div>

    </section>

    <!-- Student Search -->
    <div class="bg-white p-4 rounded shadow mb-4">
      <form method="get" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="students">
        <input name="search" placeholder="Search..." value="<?= h($search) ?>" class="border p-2 w-1/3" />
        <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
      </form>
    </div>

    <!-- Student Table -->
    <div class="bg-white p-4 rounded shadow">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2">Name</th>
            <th class="p-2">GR No</th>
            <th class="p-2">Enrollment</th>
            <th class="p-2">Class</th>
            <th class="p-2">Sem</th>
            <th class="p-2">Batch</th>
            <th class="p-2">Year</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$students): ?>
          <tr><td colspan="7" class="p-4 text-center text-gray-500">No students found.</td></tr>
        <?php else: ?>
          <?php foreach ($students as $s): ?>
            <tr class="border-t">
              <td class="p-2"><?= h($s['first_name'].' '.$s['last_name']) ?></td>
              <td class="p-2"><?= h($s['gr_no']) ?></td>
              <td class="p-2"><?= h($s['enrollment_no']) ?></td>
              <td class="p-2"><?= h($s['class']) ?></td>
              <td class="p-2"><?= h($s['semester']) ?></td>
              <td class="p-2"><?= h($s['batch']) ?></td>
              <td class="p-2"><?= h($s['academic_year']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  <?php endif; ?>

  <!-- ============================
        FACULTY SECTION
  ============================ -->
  <?php if ($tab === 'faculty'): ?>

    <!-- Faculty Forms -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

      <!-- Add Faculty -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Add Faculty</h2>
        <form method="post" action="../backend/add_faculty.php" class="space-y-2">

          <input required name="faculty_id" placeholder="Faculty ID" class="border p-2 w-full" />

          <div class="flex gap-2">
            <input required name="first_name" placeholder="First name" class="border p-2 w-1/3" />
            <input name="middle_name" placeholder="Middle name" class="border p-2 w-1/3" />
            <input required name="last_name" placeholder="Last name" class="border p-2 w-1/3" />
          </div>

          <input required name="department" placeholder="Department" class="border p-2 w-full" />
          <input required name="designation" placeholder="Designation" class="border p-2 w-full" />
          <input required name="academic_year" placeholder="Academic Year (2023-24)" class="border p-2 w-full" />

          <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Add Faculty</button>
        </form>
      </div>

      <!-- Upload Excel -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Upload Faculty Excel</h2>
        <form method="post" action="../backend/upload_faculty_excel.php" enctype="multipart/form-data" class="space-y-3">
          <input type="text" name="academic_year" placeholder="Academic Year" required class="border p-2 w-full">
          <input type="file" name="file" accept=".csv,.xlsx,.xls" required>
          <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Upload</button>
        </form>
      </div>

    </section>

    <!-- Faculty Search -->
    <div class="bg-white p-4 rounded shadow mb-4">
      <form method="get" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="faculty">
        <input name="faculty_search" placeholder="Search Faculty..." value="<?= h($facultySearch) ?>" class="border p-2 w-1/3" />
        <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
      </form>
    </div>         

    <!-- Faculty Table -->
    <div class="bg-white p-4 rounded shadow">
      <form method="post" action="../backend/delete_faculty.php">
        <button class="mb-3 bg-indigo-600 text-white px-4 py-2 rounded">Delete Selected</button>

        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-2">Select</th>
              <th class="p-2">Name</th>
              <th class="p-2">Faculty ID</th>
              <th class="p-2">Department</th>
              <th class="p-2">Designation</th>
              <th class="p-2">Acad. Year</th>
            </tr>
          </thead>
          <tbody>

          <?php if (!$faculty): ?>
            <tr><td colspan="6" class="p-4 text-center text-gray-500">No faculty found.</td></tr>
          <?php else: ?>
            <?php foreach ($faculty as $f): ?>
              <tr class="border-t">
                <td class="p-2">
                  <input type="checkbox" name="faculty_ids[]" value="<?= $f['id'] ?>">
                </td>
                <td class="p-2"><?= h($f['first_name'].' '.$f['last_name']) ?></td>
                <td class="p-2"><?= h($f['faculty_id']) ?></td>
                <td class="p-2"><?= h($f['department']) ?></td>
                <td class="p-2"><?= h($f['designation']) ?></td>
                <td class="p-2"><?= h($f['academic_year']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>

          </tbody>
        </table>
      </form>
    </div>
    

  <?php endif; ?>


  <?php if ($tab === 'nba'): ?>

<div class="bg-white p-6 rounded shadow">

  <h2 class="text-2xl font-bold mb-4">NBA Accreditation Criteria</h2>
  <p class="text-gray-600 mb-6">
    Select a criterion to upload documents or view details.
  </p>

  <div class="space-y-4">

<?php
$criteria = [
    "4 - Student Performance" => [
        "4.1 - Enrollment Ratio (20)",
        "4.2 - Success Rate in the Stipulated Period of Program (20)",
        "4.3 - Academic Performance in Second Year (10)",
        "4.4 - Placement and Career Progression (30)",
        "4.5 - Professional Societies and Activities"
    ],

    "3 - Course Outcomes (CO) & PO Mapping" => [
        "3.1 - CO Attainment",
        "3.2 - PO Mapping",
    ],

    "5 - Faculty Information" => [
        "5.1 - Faculty Qualification",
        "5.2 - Faculty–Student Ratio",
    ],

    "6 - Infrastructure & Facilities" => [
        "6.1 - Labs",
        "6.2 - Classrooms",
        "6.3 - Library",
    ],

    "7 - Continuous Improvement" => [
        "7.1 - Previous Year Improvements",
        "7.2 - Achieved Outcomes",
    ]
];
?>

<?php foreach ($criteria as $main => $subs): ?>
  <div class="border rounded p-4 bg-gray-50 mb-4">

    <!-- MAIN CRITERIA -->
    <h3 class="font-semibold text-lg"><?= $main ?></h3>

    <!-- SUB-CRITERIA -->
    <?php if (!empty($subs)): ?>
      <ul class="mt-3 ml-4 list-disc text-gray-700">

        <?php foreach ($subs as $s): ?>
          <li class="flex items-center justify-between">

            <span><?= $s ?></span>

            <a href="nba_page.php?subcriteria=<?= urlencode($s) ?>&main=<?= urlencode($main) ?>"
               class="bg-green-600 text-white px-2 py-1 rounded ml-4">
               Open
            </a>
          </li>
        <?php endforeach; ?>

      </ul>
    <?php endif; ?>

  </div>
<?php endforeach; ?>


</div>

</div>

<?php endif; ?>


</div>
</body>
</html>
