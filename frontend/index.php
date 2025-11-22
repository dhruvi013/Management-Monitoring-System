<?php
// frontend/index.php
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/helpers.php';

// Fetch students
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

// Fetch distinct batches for dropdown
$batchStmt = $pdo->query("SELECT DISTINCT batch FROM students WHERE batch IS NOT NULL AND batch != '' ORDER BY batch");
$batches = $batchStmt->fetchAll();

$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Department Monitoring - Students</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto">

  <header class="mb-6">
    <h1 class="text-2xl font-bold">Department Monitoring — Student Records</h1>
    <p class="text-sm text-gray-600">Add single students, upload CSV, or transfer an entire batch.</p>
  </header>

  <?php if ($msg): ?>
    <div class="<?= $type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700' ?> border px-4 py-3 rounded mb-4">
      <?= h($msg) ?>
    </div>
  <?php endif; ?>

  <!-- TWO COLUMN SECTION -->
  <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    <!-- Add Student Form -->
    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-2">Add Student (Form)</h2>
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
        <input required name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="border p-2 w-full" />
        <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Add Student</button>
      </form>
    </div>

    <!-- BULK UPLOAD CSV -->
    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-2">Bulk Upload (Excel/CSV)</h2>
      <p class="text-sm text-gray-600 mb-2">
        Excel/CSV should contain: <b>name, gr_no, enrollment_no, class</b>  
        (We will auto-split the name into first/middle/last)
      </p>
      <form method="post" action="../backend/upload_file.php" enctype="multipart/form-data">
        <label class="block">
          <span class="text-sm text-gray-600">Select Batch for this Upload</span>
          <input type="text" name="batch" required placeholder="e.g., B1" class="border p-2 w-full" />
        </label>
        <label class="block">
          <span class="text-sm text-gray-600">Academic Year</span>
          <input type="text" name="academic_year" required placeholder="e.g., 2023-24" class="border p-2 w-full" />
        </label>
        <input type="file" name="file" accept=".csv,.xlsx,.xls,.ods" required>
        <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Upload</button>
      </form>
    </div>

  </section>

  <!-- BATCH TRANSFER -->
  <div class="bg-white p-4 rounded shadow mb-6">
    <h2 class="font-semibold mb-2">Transfer an Entire Batch</h2>
    <form method="post" action="../backend/transfer.php" class="space-y-3">
      <label class="block">
        <span class="text-sm">Select Batch:</span>
        <select name="batch" required class="border p-2 w-full">
          <option value="">-- Select Batch --</option>
          <?php foreach ($batches as $b): ?>
            <option value="<?= h($b['batch']) ?>"><?= h($b['batch']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="block">
        <span class="text-sm">Action:</span>
        <select name="action" required class="border p-2 w-full">
          <option value="transfer">Transfer to Next Academic Year</option>
          <option value="no_transfer">Do NOT Transfer</option>
        </select>
      </label>
      <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Apply to Batch</button>
    </form>
  </div>

  <!-- SEARCH BAR -->
  <div class="bg-white p-4 rounded shadow mb-4">
    <form method="get" class="flex items-center gap-2">
      <input name="search" placeholder="Search name, GR, enrollment" value="<?= h($search) ?>" class="border p-2 w-1/3" />
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
      <a href="index.php" class="text-sm text-gray-600 ml-2">Clear</a>
    </form>
  </div>

  <!-- STUDENTS TABLE -->
  <div class="bg-white p-4 rounded shadow">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-2">Name</th>
          <th class="p-2">GR No</th>
          <th class="p-2">Enrollment</th>
          <th class="p-2">Class</th>
          <th class="p-2">Semester</th>
          <th class="p-2">Batch</th>
          <th class="p-2">Academic Year</th>
        </tr>
      </thead>
      <tbody>
      <?php if (count($students) === 0): ?>
        <tr><td colspan="7" class="p-4 text-center text-gray-500">No students found.</td></tr>
      <?php else: ?>
        <?php foreach ($students as $s): ?>
          <tr class="border-t">
            <td class="p-2"><?= h($s['first_name'] . ' ' . ($s['middle_name'] ? $s['middle_name'].' ' : '') . $s['last_name']) ?></td>
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

</div>
</body>
</html>
