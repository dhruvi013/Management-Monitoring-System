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
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              <input type="number" name="intake" placeholder="Intake (Sanctioned Capacity)" class="w-full border p-2 rounded" required>
              <input type="number" name="admitted" placeholder="Admitted (Actual Students)" class="w-full border p-2 rounded" required>

              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>

          <!-- Display Marks -->
          <div id="marks-display-41" class="mt-6 p-4 bg-white rounded-lg border-2 border-blue-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks</h3>
              <p class="text-gray-600 text-sm mb-3">Based on average enrollment ratio of last 3 years</p>
              <div id="marks-content-41" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>
      </div>

      <script>
      // Fetch and display marks for 4.1
      fetch('../backend/NBA/get_marks.php?criteria=4.1')
          .then(response => response.json())
          .then(data => {
              const container = document.getElementById('marks-content-41');
              if (data.success) {
                  let historyHTML = '';
                  if (data.history && data.history.length > 0) {
                      historyHTML = '<div class="mt-3 text-sm"><p class="font-semibold mb-1">Last 3 Years:</p><ul class="list-disc list-inside">';
                      data.history.forEach(h => {
                          historyHTML += `<li>${h.academic_year}: ${parseFloat(h.enrollment_ratio).toFixed(2)}%</li>`;
                      });
                      historyHTML += '</ul></div>';
                  }
                  
                  container.innerHTML = `
                      <div class="text-4xl font-bold text-blue-600 mb-2">${parseFloat(data.marks).toFixed(2)} / 20</div>
                      <p class="text-gray-700">Current Enrollment Ratio: ${parseFloat(data.enrollment_ratio).toFixed(2)}%</p>
                      <p class="text-gray-700">Academic Year: ${data.academic_year}</p>
                      ${historyHTML}
                  `;
              } else {
                  container.innerHTML = '<p class="text-gray-500">No data available yet. Submit the form to calculate marks.</p>';
              }
          })
          .catch(error => {
              document.getElementById('marks-content-41').innerHTML = '<p class="text-red-500">Error loading marks</p>';
          });
      </script>
  <?php } ?>




  <!-- ************************************
       4.2 - Success Rate Form (Combined)
  ************************************* -->
  <?php if ($criteria === "4.2 - Success Rate in the Stipulated Period of Program (20)") { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800">4.2 - Success Rate Form</h2>
          <p class="text-sm text-gray-700 mb-4">This form calculates both 4.2.1 (Success without backlog - 15 marks) and 4.2.2 (Success in stipulated period - 5 marks)</p>

          <form method="post" action="../backend/NBA/save_42.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              
              <div class="grid grid-cols-2 gap-4">
                  <input type="number" id="admitted_degree" name="admitted_degree" placeholder="Admitted in Degree (1st Year)" class="w-full border p-2 rounded" required min="0">
                  <input type="number" id="admitted_d2d" name="admitted_d2d" placeholder="Admitted via Lateral Entry (D2D)" class="w-full border p-2 rounded" required min="0">
              </div>

              <div class="bg-blue-100 p-3 rounded">
                  <label class="font-semibold text-gray-700">Total Admitted (Auto-calculated):</label>
                  <input type="number" id="total_admitted" name="total_admitted" class="w-full border p-2 rounded mt-1 bg-gray-100" readonly>
              </div>

              <input type="number" name="graduated_wo_back" placeholder="Graduated WITHOUT Backlog (for 4.2.1)" class="w-full border p-2 rounded" required min="0">
              
              <input type="number" name="graduated_w_back" placeholder="Graduated in Stipulated Time (for 4.2.2)" class="w-full border p-2 rounded" required min="0">

              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data & Calculate Marks</button>
          </form>

          <!-- Display Marks -->
          <div id="marks-display-42" class="mt-6 p-4 bg-white rounded-lg border-2 border-green-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks</h3>
              <p class="text-gray-600 text-sm mb-3">Based on average success index of last 3 batches</p>
              <div id="marks-content-42" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>
      </div>

      <script>
      // Auto-calculate total admitted
      function calculateTotal() {
          const degree = parseInt(document.getElementById('admitted_degree').value) || 0;
          const d2d = parseInt(document.getElementById('admitted_d2d').value) || 0;
          document.getElementById('total_admitted').value = degree + d2d;
      }

      document.getElementById('admitted_degree').addEventListener('input', calculateTotal);
      document.getElementById('admitted_d2d').addEventListener('input', calculateTotal);

      // Fetch and display marks for 4.2
      fetch('../backend/NBA/get_marks.php?criteria=4.2')
          .then(response => response.json())
          .then(data => {
              const container = document.getElementById('marks-content-42');
              if (data.success) {
                  container.innerHTML = `
                      <div class="grid grid-cols-3 gap-4 mb-4">
                          <div class="bg-blue-50 p-3 rounded">
                              <p class="text-sm text-gray-600">4.2.1 - Without Backlog</p>
                              <p class="text-2xl font-bold text-blue-600">${parseFloat(data.marks_421).toFixed(2)} / 15</p>
                              <p class="text-xs text-gray-500">SI: ${parseFloat(data.success_index_421).toFixed(4)}</p>
                          </div>
                          <div class="bg-purple-50 p-3 rounded">
                              <p class="text-sm text-gray-600">4.2.2 - In Stipulated Time</p>
                              <p class="text-2xl font-bold text-purple-600">${parseFloat(data.marks_422).toFixed(2)} / 5</p>
                              <p class="text-xs text-gray-500">SI: ${parseFloat(data.success_index_422).toFixed(4)}</p>
                          </div>
                          <div class="bg-green-50 p-3 rounded">
                              <p class="text-sm text-gray-600">Total 4.2 Marks</p>
                              <p class="text-3xl font-bold text-green-600">${parseFloat(data.total_marks).toFixed(2)} / 20</p>
                          </div>
                      </div>
                      <p class="text-gray-700 text-sm">Academic Year: ${data.academic_year}</p>
                  `;
              } else {
                  container.innerHTML = '<p class="text-gray-500">No data available yet. Submit the form to calculate marks.</p>';
              }
          })
          .catch(error => {
              document.getElementById('marks-content-42').innerHTML = '<p class="text-red-500">Error loading marks</p>';
          });
      </script>
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
