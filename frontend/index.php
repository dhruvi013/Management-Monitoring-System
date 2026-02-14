<?php
// frontend/index.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

  <header class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">Department Monitoring System</h1>
        <p class="text-sm text-gray-600">Manage Students & Faculty</p>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['email']) ?></span>
        <a href="../backend/auth_logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
    </div>
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
    "1 - Vision, Mission and Program Educational Objectives" => [
        "1.1 - State the Vision and Mission of the Department and Institute (5)",
        "1.2 - State the Program Educational Objectives (PEOs) (5)",
        "1.3 - Indicate where and how the Vision, Mission and PEOs are published and disseminated (15)",
        "1.4 - State the process for defining the Vision and Mission of the Department, and PEOs of the program (15)",
        "1.5 - Establish consistency of PEOs with Mission of the Department (10)"
    ],
    "2 - Program Curriculum and Teaching-Learning Processes" => [
        "2.1.1 - State the process for designing the program curriculum (10)",
        "2.1.2 - Structure of the Curriculum (5)",
        "2.1.3 - State the components of the curriculum (5)",
        "2.1.4 - State the process used to identify extent of compliance of the curriculum for attaining POs & PSOs (10)",
        "2.2.1 - Describe the Process followed to improve quality of Teaching Learning (15)",
        "2.2.2 - Quality of end semester examination, internal semester question papers, assignments and evaluation (15)",
        "2.2.3 - Quality of student projects (20)",
        "2.2.4 - Initiatives related to industry interaction (10)",
        "2.2.5 - Initiatives related to industry internship/summer training (10)"
    ],
    "3 - Course Outcomes (CO) & PO Mapping" => [
        "3.1 - Establish the correlation between the courses and the POs & PSOs (10)",
        "3.2.1 - Assessment tools and processes (COs) (10)",
        "3.2.2 - Attainment of Course Outcomes (10)",
        "3.3.1 - Assessment tools and processes (POs & PSOs) (10)",
        "3.3.2 - Provide results of evaluation of each PO & PSO (20)"
    ],
    "4 - Student Performance" => [
        "4.1 - Enrollment Ratio (20)",
        "4.2 - Success Rate in the Stipulated Period of Program (20)",
        "4.3 - Academic Performance in Second Year (10)",
        "4.4 - Placement and Career Progression (30)",
        "4.5 - Professional Societies and Activities (20)",
        "4.5.1 - Professional Chapters and Events (5)",
        "4.5.2 - Publications (Magazine/Newsletter) (5)",
        "4.5.3 - Student Participation in Events (10)"
    ],

    "5 - Faculty Information" => [
        "5.1 - Student-Faculty Ratio (SFR) (20)",
        "5.2 - Faculty Cadre proportion (20)",
        "5.3 - Faculty Qualification (20)",
        "5.4 - Faculty Retention (10)",
        "5.5 - Faculty Competency in correlation to program specific criteria (10)",
        "5.6 - Innovations by the faculty in teaching and learning (10)",
        "5.7 - Faculty as participants in Faculty development/trainig activities/ STTPS (15)",
        "5.8 - Reasearch and Development (75)",
        "5.8.1 - Academic Reasearch (20)",
        "5.8.2 - Sponsored Research (20)",
        "5.8.3 - Development Activities (15)",
        "5.8.4 - Consultancy (from industry) (20)",
        "5.9 - Faculty performance appraisal and development system (FPADS) (10)",
        "5.10 - Visiting/ Adjunct/ Emeritus Faculty etc. (10)" ,
    ],

    "6 - Facilities and Technical Support" => [
        "6.1 - Adequate and well equipped laboratories, and technical manpower (40)",
        "6.2 - Laboratories: Maintenance and overall ambience (10)",
        "6.3 - Safety measures in laboratories (10)",
        "6.4 - Project laboratory/Facilities (20)",
    ],

    "7 - Continuous Improvement" => [
        "7.1 - POs and PSOs Assessment (30)",
        "7.2 - Academic Audit and Improvements (15)",
        "7.3 - Placement, Higher Studies and Entrepreneurship (10)",
        "7.4 - Quality of Admitted Students (20)",
    ],

    "8 - First Year Academics" => [
        "8.1 - First Year Student-Faculty Ratio (FYSFR) (5)",
        "8.2 - Qualification of Faculty Teaching First Year Common Courses (5)",
        "8.3 - First Year Academic Performance (10)",
        "8.4.1 - Description of Assessment Processes (5)",
        "8.4.2 - Attainment of Course Outcomes (5)",
        "8.5.1 - Evaluation of POs/PSOs (10)",
        "8.5.2 - Actions Taken based on PO/PSO Evaluation (10)",
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
