<?php
require_once __DIR__ . '/../backend/helpers.php';

$criteria = $_GET['criteria'] ?? null;
$sub = $_GET['subcriteria'] ?? null;
$main = $_GET['main'] ?? null;

// Heading Text - prioritize subcriteria if available
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
  <?php if ($main): ?>
    <p class="text-sm text-gray-500 mb-4">Parent: <?= h($main) ?></p>
  <?php endif; ?>
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

  <?php if ($sub === "4.1 - Enrollment Ratio (20)") { ?>
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
  <?php if ($sub === "4.2 - Success Rate in the Stipulated Period of Program (20)") { ?>
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




  <!-- ************************************
       4.3 - Academic Performance in Second Year (10 marks)
  ************************************* -->
  <?php if ($sub === "4.3 - Academic Performance in Second Year (10)") { ?>
      <div class="p-6 border-l-4 border-purple-500 bg-purple-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-purple-800">4.3 - Academic Performance in Second Year</h2>
          <p class="text-sm text-gray-700 mb-4">Calculate Academic Performance Index (API) based on CGPA and success rate</p>

          <form method="post" action="../backend/NBA/save_43.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              
              <div class="grid grid-cols-2 gap-4">
                  <input type="number" id="admitted_degree_43" name="admitted_degree" placeholder="Admitted in Degree (1st Year)" class="w-full border p-2 rounded" required min="0">
                  <input type="number" id="admitted_d2d_43" name="admitted_d2d" placeholder="Admitted via D2D" class="w-full border p-2 rounded" required min="0">
              </div>

              <div class="bg-blue-100 p-3 rounded">
                  <label class="font-semibold text-gray-700">Total Admitted (Auto-calculated):</label>
                  <input type="number" id="total_admitted_43" name="total_admitted" class="w-full border p-2 rounded mt-1 bg-gray-100" readonly>
              </div>

              <div class="grid grid-cols-2 gap-4">
                  <input type="number" step="0.01" id="sem3_avg_sgpa" name="sem3_avg_sgpa" placeholder="Sem 3 Average SGPA" class="w-full border p-2 rounded" required min="0" max="10">
                  <input type="number" step="0.01" id="sem4_avg_sgpa" name="sem4_avg_sgpa" placeholder="Sem 4 Average SGPA" class="w-full border p-2 rounded" required min="0" max="10">
              </div>

              <div class="grid grid-cols-2 gap-4">
                  <input type="number" id="sem3_credit" name="sem3_credit" placeholder="Sem 3 Credits" class="w-full border p-2 rounded" required min="0">
                  <input type="number" id="sem4_credit" name="sem4_credit" placeholder="Sem 4 Credits" class="w-full border p-2 rounded" required min="0">
              </div>

              <div class="bg-yellow-100 p-3 rounded">
                  <label class="font-semibold text-gray-700">Total Mean CGPA (Auto-calculated):</label>
                  <input type="number" step="0.01" id="total_mean_cgpa" name="total_mean_cgpa" class="w-full border p-2 rounded mt-1 bg-gray-100" readonly>
                  <p class="text-xs text-gray-600 mt-1">Formula: (Sem3_SGPA × Sem3_Credits + Sem4_SGPA × Sem4_Credits) / (Sem3_Credits + Sem4_Credits)</p>
              </div>

              <input type="number" name="success_2ndyear" placeholder="Successful Students (Promoted to 3rd Year)" class="w-full border p-2 rounded" required min="0">
              
              <input type="number" name="students_appeared" placeholder="Students Appeared in Examination" class="w-full border p-2 rounded" required min="0">

              <button class="bg-purple-600 text-white px-4 py-2 rounded w-full">Save Data & Calculate Marks</button>
          </form>

          <!-- Display Marks -->
          <div id="marks-display-43" class="mt-6 p-4 bg-white rounded-lg border-2 border-purple-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks</h3>
              <p class="text-gray-600 text-sm mb-3">Based on Academic Performance Index (API)</p>
              <div id="marks-content-43" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>
      </div>

      <script>
      // Auto-calculate total admitted for 4.3
      function calculateTotal43() {
          const degree = parseInt(document.getElementById('admitted_degree_43').value) || 0;
          const d2d = parseInt(document.getElementById('admitted_d2d_43').value) || 0;
          document.getElementById('total_admitted_43').value = degree + d2d;
      }

      document.getElementById('admitted_degree_43').addEventListener('input', calculateTotal43);
      document.getElementById('admitted_d2d_43').addEventListener('input', calculateTotal43);

      // Auto-calculate total mean CGPA
      function calculateMeanCGPA() {
          const sem3_sgpa = parseFloat(document.getElementById('sem3_avg_sgpa').value) || 0;
          const sem4_sgpa = parseFloat(document.getElementById('sem4_avg_sgpa').value) || 0;
          const sem3_credit = parseFloat(document.getElementById('sem3_credit').value) || 0;
          const sem4_credit = parseFloat(document.getElementById('sem4_credit').value) || 0;
          
          const total_credits = sem3_credit + sem4_credit;
          if (total_credits > 0) {
              const mean_cgpa = ((sem3_sgpa * sem3_credit) + (sem4_sgpa * sem4_credit)) / total_credits;
              document.getElementById('total_mean_cgpa').value = mean_cgpa.toFixed(2);
          } else {
              document.getElementById('total_mean_cgpa').value = '';
          }
      }

      document.getElementById('sem3_avg_sgpa').addEventListener('input', calculateMeanCGPA);
      document.getElementById('sem4_avg_sgpa').addEventListener('input', calculateMeanCGPA);
      document.getElementById('sem3_credit').addEventListener('input', calculateMeanCGPA);
      document.getElementById('sem4_credit').addEventListener('input', calculateMeanCGPA);

      // Fetch and display marks for 4.3
      fetch('../backend/NBA/get_marks.php?criteria=4.3')
          .then(response => response.json())
          .then(data => {
              const container = document.getElementById('marks-content-43');
              if (data.success) {
                  container.innerHTML = `
                      <div class="text-4xl font-bold text-purple-600 mb-2">${parseFloat(data.marks).toFixed(2)} / 10</div>
                      <p class="text-gray-700">API: ${parseFloat(data.api).toFixed(4)}</p>
                      <p class="text-gray-700">Mean CGPA: ${parseFloat(data.mean_cgpa).toFixed(2)}</p>
                      <p class="text-gray-700">Success Rate: ${parseFloat(data.success_rate).toFixed(2)}%</p>
                      <p class="text-gray-700 text-sm mt-2">Academic Year: ${data.academic_year}</p>
                  `;
              } else {
                  container.innerHTML = '<p class="text-gray-500">No data available yet. Submit the form to calculate marks.</p>';
              }
          })
          .catch(error => {
              document.getElementById('marks-content-43').innerHTML = '<p class="text-red-500">Error loading marks</p>';
          });
      </script>
  <?php } ?>


  <!-- ************************************
       4.4 - Placement and Career Progression (30 marks)
  ************************************* -->
  <?php if ($sub === "4.4 - Placement and Career Progression (30)") { ?>
      <div class="p-6 border-l-4 border-teal-500 bg-teal-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-teal-800">4.4 - Placement and Career Progression</h2>
          <p class="text-sm text-gray-700 mb-4">Track placements, higher studies, and entrepreneurship for final year students</p>

          <form method="post" action="../backend/NBA/save_44.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              
              <input type="number" name="final_year_total" placeholder="Total Final Year Students (N)" class="w-full border p-2 rounded" required min="1">

              <div class="bg-blue-50 p-4 rounded border border-blue-200">
                  <h3 class="font-semibold text-gray-700 mb-3">Career Outcomes</h3>
                  
                  <div class="space-y-3">
                      <div>
                          <label class="text-sm text-gray-600">Students Placed (x)</label>
                          <input type="number" name="placed" placeholder="Number of students placed in companies/government" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500 mt-1">On/off campus recruitment in companies or government sector</p>
                      </div>

                      <div>
                          <label class="text-sm text-gray-600">Higher Studies (y)</label>
                          <input type="number" name="higher_studies" placeholder="Number of students admitted to higher studies" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500 mt-1">With valid qualifying scores (GATE, GRE, GMAT, etc.)</p>
                      </div>

                      <div>
                          <label class="text-sm text-gray-600">Entrepreneurs (z)</label>
                          <input type="number" name="entrepreneur" placeholder="Number of students turned entrepreneur" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500 mt-1">In engineering/technology domain</p>
                      </div>
                  </div>
              </div>

              <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                  <p class="text-sm font-semibold text-gray-700">Formula:</p>
                  <p class="text-xs text-gray-600 mt-1">Assessment Points = 30 × Average of 3 years of [(x + y + z) / N]</p>
              </div>

              <button class="bg-teal-600 text-white px-4 py-2 rounded w-full">Save Data & Calculate Marks</button>
          </form>

          <!-- Display Marks -->
          <div id="marks-display-44" class="mt-6 p-4 bg-white rounded-lg border-2 border-teal-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks</h3>
              <p class="text-gray-600 text-sm mb-3">Based on 3-year average assessment index</p>
              <div id="marks-content-44" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>
      </div>

      <script>
      // Fetch and display marks for 4.4
      fetch('../backend/NBA/get_marks.php?criteria=4.4')
          .then(response => response.json())
          .then(data => {
              const container = document.getElementById('marks-content-44');
              if (data.success) {
                  let historyHTML = '';
                  if (data.history && data.history.length > 0) {
                      historyHTML = '<div class="mt-4 text-sm"><p class="font-semibold mb-2">Last 3 Years Data:</p><div class="grid grid-cols-1 gap-2">';
                      data.history.forEach(h => {
                          const percentage = (parseFloat(h.assessment_index) * 100).toFixed(2);
                          historyHTML += `
                              <div class="bg-gray-50 p-2 rounded">
                                  <p class="font-semibold">${h.academic_year}</p>
                                  <p class="text-xs">Placed: ${h.placed} | Higher Studies: ${h.higher_studies} | Entrepreneurs: ${h.entrepreneur}</p>
                                  <p class="text-xs">Total: ${h.final_year_total} | Index: ${parseFloat(h.assessment_index).toFixed(4)} (${percentage}%)</p>
                              </div>
                          `;
                      });
                      historyHTML += '</div></div>';
                  }
                  
                  const currentPercentage = (parseFloat(data.assessment_index) * 100).toFixed(2);
                  container.innerHTML = `
                      <div class="text-4xl font-bold text-teal-600 mb-2">${parseFloat(data.marks).toFixed(2)} / 30</div>
                      <div class="grid grid-cols-3 gap-3 mb-3">
                          <div class="bg-blue-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Placed</p>
                              <p class="text-lg font-bold text-blue-600">${data.placed}</p>
                          </div>
                          <div class="bg-green-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Higher Studies</p>
                              <p class="text-lg font-bold text-green-600">${data.higher_studies}</p>
                          </div>
                          <div class="bg-purple-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Entrepreneurs</p>
                              <p class="text-lg font-bold text-purple-600">${data.entrepreneur}</p>
                          </div>
                      </div>
                      <p class="text-gray-700">Assessment Index: ${parseFloat(data.assessment_index).toFixed(4)} (${currentPercentage}%)</p>
                      <p class="text-gray-700">Total Final Year: ${data.final_year_total}</p>
                      <p class="text-gray-700 text-sm mt-2">Academic Year: ${data.academic_year}</p>
                      ${historyHTML}
                  `;
              } else {
                  container.innerHTML = '<p class="text-gray-500">No data available yet. Submit the form to calculate marks.</p>';
              }
          })
          .catch(error => {
              document.getElementById('marks-content-44').innerHTML = '<p class="text-red-500">Error loading marks</p>';
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
