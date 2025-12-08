<?php
require_once __DIR__ . '/../backend/helpers.php';

$criteria = $_GET['criteria'] ?? null;
$sub = $_GET['subcriteria'] ?? null;

// Heading Text
$title = $sub ?: $criteria;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= h($title) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg">

  <a href="index.php?tab=nba" class="text-blue-600 text-sm font-semibold">&larr; Back to NBA</a>

  <h1 class="text-3xl font-bold mt-4 mb-2"><?= h($title) ?></h1>
  <p class="text-gray-500 mb-8">Please enter required details and upload documents for this criteria.</p>

  <!-- UNIVERSAL UPLOAD BOX -->
  <div class="border rounded-lg p-4 mb-8 bg-gray-50">
      <h3 class="font-semibold text-gray-700 mb-2">Upload Supporting Document</h3>
      <form method="post" action="../backend/upload_nba.php" enctype="multipart/form-data" class="flex gap-3">
          <input type="hidden" name="criteria" value="<?= h($title) ?>">
          <input type="file" name="file" class="border p-2 w-full rounded" required>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Upload</button>
      </form>
  </div>


  <!-- ************************************
       MAIN CRITERIA FORMS
  ************************************* -->

  <?php if ($criteria === "4.1 - Enrollment Ratio (20)") { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800">4.1 - Enrollment Ratio Form</h2>

          <form method="post" action="../backend/NBA/save_41.php" class="space-y-4">
              <input type="text" name="intake" placeholder="Academic Year (2023-24)" class="w-full border p-2 rounded" required>
              <input type="number" name="admitted" placeholder="Intake" class="w-full border p-2 rounded" required>
              <input type="number" name="year" placeholder="Admitted" class="w-full border p-2 rounded" required>

              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
      </div>
  <?php } ?>


  <!-- ************************************
        SUB-CRITERIA FORMS
  ************************************* -->


  <?php if ($sub === "4.2.1 - Success Rate in 1st Year") { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800">4.2.1 - Success Rate (1st Year)</h2>

          <form method="post" action="../backend/nba/save_421.php" class="space-y-4">
              <input type="number" name="eligible_students" placeholder="Eligible Students" class="w-full border p-2 rounded" required>
              <input type="number" name="passed_students" placeholder="Students Passed" class="w-full border p-2 rounded" required>
              <input type="text" name="year" placeholder="Academic Year" class="w-full border p-2 rounded" required>

              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Record</button>
          </form>
      </div>
  <?php } ?>


  <?php if ($sub === "4.2.2 - Success Rate in 2nd Year") { ?>
      <div class="p-6 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-yellow-800">4.2.2 - Success Rate (2nd Year)</h2>

          <form method="post" action="../backend/nba/save_422.php" class="space-y-4">
              <input type="number" name="students_admitted" placeholder="Students Admitted" class="w-full border p-2 rounded" required>
              <input type="number" name="graduated_on_time" placeholder="Graduated on Time" class="w-full border p-2 rounded" required>
              <input type="text" name="year" placeholder="Academic Year" class="w-full border p-2 rounded" required>

              <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
      </div>
  <?php } ?>


  <!-- 3.1 CO Attainment -->
  <?php if ($sub === "3.1 - CO Attainment") { ?>
      <div class="p-6 border-l-4 border-purple-500 bg-purple-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-purple-800">3.1 - CO Attainment Form</h2>

          <form method="post" action="../backend/nba/save_31.php" class="space-y-4">
              <input type="text" name="course_code" placeholder="Course Code" class="w-full border p-2 rounded" required>
              <input type="number" name="co1" placeholder="CO1 (%)" class="w-full border p-2 rounded">
              <input type="number" name="co2" placeholder="CO2 (%)" class="w-full border p-2 rounded">
              <input type="number" name="co3" placeholder="CO3 (%)" class="w-full border p-2 rounded">

              <button class="bg-purple-600 text-white px-4 py-2 rounded w-full">Save</button>
          </form>
      </div>
  <?php } ?>


  <!-- 3.2 PO Mapping -->
  <?php if ($sub === "3.2 - PO Mapping") { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800">3.2 - PO Mapping Form</h2>

          <form method="post" action="../backend/nba/save_32.php" class="space-y-4">
              <input type="text" name="course_code" placeholder="Course Code" class="w-full border p-2 rounded" required>
              <input type="number" name="po1" placeholder="PO1 Value" class="w-full border p-2 rounded">
              <input type="number" name="po2" placeholder="PO2 Value" class="w-full border p-2 rounded">
              <input type="number" name="po3" placeholder="PO3 Value" class="w-full border p-2 rounded">

              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Mapping</button>
          </form>
      </div>
  <?php } ?>


  <!-- Placement -->
  <?php if ($sub === "4.3.1 - Placements") { ?>
      <div class="p-6 border-l-4 border-teal-500 bg-teal-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-teal-800">4.3.1 - Placement Details</h2>

          <form method="post" action="../backend/nba/save_431.php" class="space-y-4">
              <input type="number" name="eligible" placeholder="Eligible Students" class="w-full border p-2 rounded">
              <input type="number" name="placed" placeholder="Students Placed" class="w-full border p-2 rounded">
              <input type="text" name="year" placeholder="Academic Year" class="w-full border p-2 rounded">

              <button class="bg-teal-600 text-white px-4 py-2 rounded w-full">Save Placement Data</button>
          </form>
      </div>
  <?php } ?>


</div>
</body>
</html>
