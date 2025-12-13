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
  <style>
    .nba-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .nba-table th, .nba-table td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
    .nba-table th { background-color: #f8fafc; font-weight: 600; }
    .nba-table tr:nth-child(even) { background-color: #f8fafc; }
    .delete-btn { color: #ef4444; font-weight: bold; cursor: pointer; }
    .delete-btn:hover { text-decoration: underline; }
  </style>
  <script>
    // Store table data globally to access for editing
    window.tableData = {};

    function editRow(criteria, index) {
        const row = window.tableData[criteria][index];
        if (!row) return;

        // Scroll to form
        const form = document.querySelector(`div[id="table-container-${criteria}"]`).previousElementSibling.previousElementSibling; 
        
        let targetForm;
        
        if(criteria === '4.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_41.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="intake"]').value = row.intake;
            targetForm.querySelector('[name="admitted"]').value = row.admitted;
            // Set ID
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if(criteria.startsWith('4.2')) {
            targetForm = document.querySelector('form[action="../backend/NBA/save_42.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="admitted_degree"]').value = row.admitted_degree || '';
            targetForm.querySelector('[name="admitted_d2d"]').value = row.admitted_d2d || '';
            
            // Trigger calculation
            if(window.calculateTotal) window.calculateTotal();
            
            if(criteria === '4.2.1') {
                targetForm.querySelector('[name="graduated_wo_back"]').value = row.graduated_wo_back;
                let idInput = targetForm.querySelector('[name="id_421"]');
                if(!idInput) {
                    idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id_421';
                    targetForm.appendChild(idInput);
                }
                idInput.value = row.id;
            } else {
                targetForm.querySelector('[name="graduated_w_back"]').value = row.graduated_w_back;
                let idInput = targetForm.querySelector('[name="id_422"]');
                if(!idInput) {
                    idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id_422';
                    targetForm.appendChild(idInput);
                }
                idInput.value = row.id;
            }
        }
        else if(criteria === '4.3') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_43.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="admitted_degree"]').value = row.admitted_degree;
            targetForm.querySelector('[name="admitted_d2d"]').value = row.admitted_d2d;
            targetForm.querySelector('[name="sem3_avg_sgpa"]').value = row.sem3_avg_sgpa;
            targetForm.querySelector('[name="sem4_avg_sgpa"]').value = row.sem4_avg_sgpa;
            targetForm.querySelector('[name="sem3_credit"]').value = row.sem3_credit;
            targetForm.querySelector('[name="sem4_credit"]').value = row.sem4_credit;
            targetForm.querySelector('[name="success_2ndyear"]').value = row.success_2ndyear;
            targetForm.querySelector('[name="students_appeared"]').value = row.students_appeared;

            if(window.calculateTotal43) window.calculateTotal43();
            if(window.calculateMeanCGPA) window.calculateMeanCGPA();

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if(criteria === '4.4') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_44.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="final_year_total"]').value = row.final_year_total;
            targetForm.querySelector('[name="placed"]').value = row.placed;
            targetForm.querySelector('[name="higher_studies"]').value = row.higher_studies;
            targetForm.querySelector('[name="entrepreneur"]').value = row.entrepreneur;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if(criteria === '4.5.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_451.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="no_of_chapters"]').value = row.no_of_chapters;
            targetForm.querySelector('[name="international_events"]').value = row.international_events;
            targetForm.querySelector('[name="national_events"]').value = row.national_events;
            targetForm.querySelector('[name="state_events"]').value = row.state_events;
            targetForm.querySelector('[name="dept_events"]').value = row.dept_events;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if(criteria === '4.5.2') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_452.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="magazine"]').value = row.magazine;
            targetForm.querySelector('[name="magazine"]').dispatchEvent(new Event('change'));
            
            targetForm.querySelector('[name="newsletter"]').value = row.newsletter;
            targetForm.querySelector('[name="newsletter"]').dispatchEvent(new Event('change'));

            if(row.magazine === 'Yes') {
                 targetForm.querySelector('[name="target_freq1"]').value = row.target_freq1;
            }
            targetForm.querySelector('[name="num_magazine"]').value = row.num_magazine;


            if(row.newsletter === 'Yes') {
                 targetForm.querySelector('[name="target_freq2"]').value = row.target_freq2;
            }
            targetForm.querySelector('[name="num_newsletter"]').value = row.num_newsletter;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if(criteria === '4.5.3') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_453.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="total_participation"]').value = row.total_participation;
            targetForm.querySelector('[name="participation_within_state"]').value = row.participation_within_state;
            targetForm.querySelector('[name="participation_outside_state"]').value = row.participation_outside_state;
            targetForm.querySelector('[name="awards"]').value = row.awards;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        // Criterion 5 Handlers
        else if (['5.1', '5.2', '5.3', '5.4', '5.5', '5.6', '5.7'].includes(criteria)) {
            const cleanCrit = criteria.replace('.', ''); 
            targetForm = document.querySelector(`form[action="../backend/NBA/save_${cleanCrit}.php"]`);
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="details"]').value = row.details;
            targetForm.querySelector('[name="value"]').value = row.value;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '5.8') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_58.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="innovation"]').value = row.innovation;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (['5.8.1', '5.8.2'].includes(criteria)) {
             const cleanCrit = criteria.replace(/\./g, '');
             targetForm = document.querySelector(`form[action="../backend/NBA/save_${cleanCrit}.php"]`);
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="details"]').value = row.details;
             targetForm.querySelector('[name="marks"]').value = row.marks;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '5.9') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_59.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="details"]').value = row.details;
             targetForm.querySelector('[name="hours"]').value = row.hours;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '5.10') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_510.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="topic"]').value = row.topic;
             targetForm.querySelector('[name="publication"]').value = row.publication;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }

        if(targetForm) {
            targetForm.scrollIntoView({ behavior: 'smooth' });
            const btn = targetForm.querySelector('button');
            if(btn) {
                btn.innerText = "Update Data";
                btn.className = "w-full bg-yellow-600 text-white p-2 rounded hover:bg-yellow-700 transition duration-200 shadow-md transform hover:scale-105";
            }
        }
    }

    async function deleteRow(id, criteria) {
        if(!confirm('Are you sure you want to delete this record?')) return;
        
        try {
            const formData = { id: id, criteria: criteria };
            const response = await fetch('../backend/NBA/delete_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const data = await response.json();
            if(data.success) {
                alert('Record deleted successfully');
                location.reload(); 
            } else {
                alert('Error: ' + data.message);
            }
        } catch(e) {
            console.error(e);
            alert('An error occurred');
        }
    }

    async function loadTable(criteria, containerId, columns) {
        try {
            const response = await fetch(`../backend/NBA/get_table_data.php?criteria=${criteria}`);
            const data = await response.json();
            const container = document.getElementById(containerId);
            
            if(data.success && data.data.length > 0) {
                // Store data
                window.tableData[criteria] = data.data;

                let html = '<div class="overflow-x-auto"><table class="nba-table"><thead><tr>';
                
                // Headers
                columns.forEach(col => {
                    html += `<th>${col.label}</th>`;
                });
                html += '<th>Action</th></tr></thead><tbody>';
                
                // Rows
                data.data.forEach((row, index) => {
                    html += '<tr>';
                    columns.forEach(col => {
                        let val = row[col.key];
                        if(col.format) val = col.format(val);
                        html += `<td>${val !== undefined ? val : ''}</td>`;
                    });
                    html += `<td>
                        <button onclick="editRow('${criteria}', ${index})" class="text-blue-600 font-bold hover:underline mr-2">Update</button>
                        <button onclick="deleteRow(${row.id}, '${criteria}')" class="delete-btn">Delete</button>
                    </td></tr>`;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-sm text-gray-500 mt-2">No records found.</p>';
            }
        } catch(e) {
            console.error(e);
            document.getElementById(containerId).innerHTML = '<p class="text-red-500">Error loading data.</p>';
        }
    }
  </script>
</head>

<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg">

  <a href="index.php?tab=nba" class="text-blue-600 text-sm font-semibold">&larr; Back to NBA</a>

  <h1 class="text-3xl font-bold mt-4 mb-2"><?= h($title) ?></h1>
  <?php if ($main): ?>
    <p class="text-sm text-gray-500 mb-4">Parent: <?= h($main) ?></p>
  <?php endif; ?>
  <!-- <p class="text-gray-500 mb-8">Please enter required details and upload documents for this criteria.</p> -->

  <!-- UNIVERSAL UPLOAD BOX -->
  <!-- <div class="border rounded-lg p-4 mb-8 bg-gray-50">
      <h3 class="font-semibold text-gray-700 mb-2">Upload Supporting Document</h3>
      <form method="post" action="../backend/upload_nba.php" enctype="multipart/form-data" class="flex gap-3">
          <input type="hidden" name="criteria" value="<?= h($title) ?>">
          <input type="file" name="file" class="border p-2 w-full rounded" required>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Upload</button>
      </form>
  </div>
 -->

  <!-- ************************************
       MAIN CRITERIA FORMS
  ************************************* -->

  <?php if ($title === "4.1 - Enrollment Ratio (20)") { ?>
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
          
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-4.1"></div>
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

          // Load Table 4.1
          loadTable('4.1', 'table-container-4.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'intake', label: 'Intake' },
              { key: 'admitted', label: 'Admitted' },
              { key: 'enrollment_ratio', label: 'Ratio (%)', format: v => parseFloat(v).toFixed(2) },
              { key: 'marks', label: 'Marks', format: v => parseFloat(v).toFixed(2) }
          ]);
      </script>
  <?php } ?>




  <!-- ************************************
       4.2 - Success Rate Form (Combined)
  ************************************* -->
  <?php if ($title === "4.2 - Success Rate in the Stipulated Period of Program (20)") { ?>
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

          <div class="mt-8 bg-blue-50 p-4 rounded">
              <h3 class="font-bold text-lg text-gray-700">4.2.1 Records (Without Backlog)</h3>
              <div id="table-container-4.2.1"></div>
          </div>

          <div class="mt-8 bg-purple-50 p-4 rounded">
              <h3 class="font-bold text-lg text-gray-700">4.2.2 Records (In Stipulated Time)</h3>
              <div id="table-container-4.2.2"></div>
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

          // Load Tables 4.2
          loadTable('4.2.1', 'table-container-4.2.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'admitted_degree', label: 'Degree' },
              { key: 'admitted_d2d', label: 'D2D' },
              { key: 'total_admitted', label: 'Total' },
              { key: 'graduated_wo_back', label: 'Grad W/O Back' },
              { key: 'success_index', label: 'SI', format: v => parseFloat(v).toFixed(2) },
              { key: 'marks', label: 'Marks', format: v => parseFloat(v).toFixed(2) }
          ]);
          loadTable('4.2.2', 'table-container-4.2.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'admitted_degree', label: 'Degree' },
              { key: 'admitted_d2d', label: 'D2D' },
              { key: 'total_admitted', label: 'Total' },
              { key: 'graduated_w_back', label: 'Grad In Time' },
              { key: 'success_index', label: 'SI', format: v => parseFloat(v).toFixed(2) },
              { key: 'marks', label: 'Marks', format: v => parseFloat(v).toFixed(2) }
          ]);
      </script>
  <?php } ?>




  <!-- ************************************
       4.3 - Academic Performance in Second Year (10 marks)
  ************************************* -->
  <?php if ($title === "4.3 - Academic Performance in Second Year (10)") { ?>
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
          
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-4.3"></div>
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
          
          loadTable('4.3', 'table-container-4.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'total_admitted', label: 'Total Admitted' },
              { key: 'success_2ndyear', label: 'Success 2nd Yr' },
              { key: 'api', label: 'API', format: v => parseFloat(v).toFixed(2) },
              { key: 'total_mean_cgpa', label: 'Mean CGPA', format: v => parseFloat(v).toFixed(2) },
              { key: 'success_rate', label: 'Success Rate (%)', format: v => parseFloat(v).toFixed(2) },
              { key: 'marks', label: 'Marks', format: v => parseFloat(v).toFixed(2) }
          ]);
      </script>
  <?php } ?>


  <!-- ************************************
       4.4 - Placement and Career Progression (30 marks)
  ************************************* -->
  <?php if ($title === "4.4 - Placement and Career Progression (30)") { ?>
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

          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-4.4"></div>
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

          loadTable('4.4', 'table-container-4.4', [
              { key: 'academic_year', label: 'Year' },
              { key: 'final_year_total', label: 'Final Yr Total' },
              { key: 'placed', label: 'Placed' },
              { key: 'higher_studies', label: 'Higher Studies' },
              { key: 'entrepreneur', label: 'Entrepreneur' },
              { key: 'assessment_index', label: 'Index', format: v => parseFloat(v).toFixed(2) },
              { key: 'marks', label: 'Marks', format: v => parseFloat(v).toFixed(2) }
          ]);
      </script>
  <?php } ?>

  


  <!-- ************************************
       4.5.1 - Professional Chapters and Events (5 marks)
  ************************************* -->
  <?php if ($title === "4.5.1 - Professional Chapters and Events (5)") { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800">4.5.1 - Professional Chapters and Events</h2>
          <p class="text-sm text-gray-700 mb-4">Track professional society chapters and organized events</p>

          <form method="post" action="../backend/NBA/save_451.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              
              <div class="bg-blue-50 p-4 rounded border border-blue-200">
                  <h3 class="font-semibold text-gray-700 mb-2">Part A: Professional Chapters (Max 3 marks)</h3>
                  <input type="number" name="no_of_chapters" placeholder="Number of Chapters" class="w-full border p-2 rounded" required min="0">
                  <p class="text-xs text-gray-500 mt-1">1 chapter = 1 mark, 2 chapters = 2 marks, 3+ chapters = 3 marks</p>
              </div>

              <div class="bg-green-50 p-4 rounded border border-green-200">
                  <h3 class="font-semibold text-gray-700 mb-3">Part B: Events Organized (Max 2 marks)</h3>
                  <div class="space-y-2">
                      <div>
                          <label class="text-sm text-gray-600">International Events</label>
                          <input type="number" name="international_events" placeholder="Number of international events" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500">1 event = 0.5 marks</p>
                      </div>
                      <div>
                          <label class="text-sm text-gray-600">National Events</label>
                          <input type="number" name="national_events" placeholder="Number of national events" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500">1 event = 1 mark</p>
                      </div>
                      <div>
                          <label class="text-sm text-gray-600">State Level Events</label>
                          <input type="number" name="state_events" placeholder="Number of state events" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500">5 events = 1 mark</p>
                      </div>
                      <div>
                          <label class="text-sm text-gray-600">Department Level Events</label>
                          <input type="number" name="dept_events" placeholder="Number of department events" class="w-full border p-2 rounded" required min="0">
                          <p class="text-xs text-gray-500">20+ events = 3 marks</p>
                      </div>
                  </div>
              </div>

              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data & Calculate Marks</button>
          </form>

          <div id="marks-display-451" class="mt-6 p-4 bg-white rounded-lg border-2 border-indigo-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks</h3>
              <div id="marks-content-451" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>

          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-4.5.1"></div>
          </div>
      </div>

      <script>
      fetch('../backend/NBA/get_marks.php?criteria=4.5.1')
          .then(response => response.json())
          .then(data => {
              const container = document.getElementById('marks-content-451');
              if (data.success) {
                  container.innerHTML = `
                      <div class="text-4xl font-bold text-indigo-600 mb-2">${parseFloat(data.total_marks).toFixed(2)} / 5</div>
                      <div class="grid grid-cols-2 gap-3 mb-3">
                          <div class="bg-blue-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Part A (Chapters)</p>
                              <p class="text-lg font-bold text-blue-600">${parseFloat(data.marks_a).toFixed(2)} / 3</p>
                              <p class="text-xs">${data.no_of_chapters} chapters</p>
                          </div>
                          <div class="bg-green-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Part B (Events)</p>
                              <p class="text-lg font-bold text-green-600">${parseFloat(data.marks_b).toFixed(2)} / 2</p>
                          </div>
                      </div>
                      <p class="text-sm text-gray-700">Intl: ${data.international_events} | National: ${data.national_events} | State: ${data.state_events} | Dept: ${data.dept_events}</p>
                      <p class="text-gray-700 text-sm mt-2">Academic Year: ${data.academic_year}</p>
                  `;
              } else {
                  container.innerHTML = '<p class="text-gray-500">No data available yet.</p>';
              }
          })
          .catch(error => {
              document.getElementById('marks-content-451').innerHTML = '<p class="text-red-500">Error loading marks</p>';
          });

          loadTable('4.5.1', 'table-container-4.5.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'no_of_chapters', label: 'Chapters' },
              { key: 'international_events', label: 'Intl Events' },
              { key: 'national_events', label: 'Nat Events' },
              { key: 'state_events', label: 'State Events' },
              { key: 'dept_events', label: 'Dept Events' },
              { key: 'marks_a', label: 'Marks A' },
              { key: 'marks_b', label: 'Marks B' },
              { key: 'total_marks', label: 'Total Marks' }
          ]);
      </script>
  <?php } ?>


<!-- ************************************
     4.5.2 - Publications (Magazine/Newsletter) (5 marks)
************************************* -->
<?php if ($title === "4.5.2 - Publications (Magazine/Newsletter) (5)") { ?>
    <div class="p-6 border-l-4 border-pink-500 bg-pink-50 rounded-lg mb-6">
        <h2 class="text-xl font-bold mb-4 text-pink-800">4.5.2 - Publications (Magazine/Newsletter)</h2>
        <p class="text-sm text-gray-700 mb-4">Track magazine and newsletter publications</p>

        <form method="post" action="../backend/NBA/save_452.php" class="space-y-4">
            <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>

            <div class="grid grid-cols-2 gap-4">

                <!-- ===================== MAGAZINE ===================== -->
                <div class="bg-blue-50 p-3 rounded">
                    <label class="font-semibold text-gray-700">Magazine</label>
                    <select name="magazine" id="magazine" class="w-full border p-2 rounded mt-2" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>

                    <!-- Frequency box -->
                    <div id="mag_freq_box" class="mt-2">
                        <input type="number" name="target_freq1" placeholder="Frequency per year"
                            class="w-full border p-2 rounded" min="0">
                        <p class="text-xs text-gray-500 mt-1">Target: 1 per year</p>
                    </div>

                    <!-- Number of magazines (when No) -->
                    <div id="mag_num_box" class="mt-2 hidden">
                        <input type="number" name="num_magazine" placeholder="Number of Magazines Published"
                            class="w-full border p-2 rounded" min="0">
                        <p class="text-xs text-gray-500 mt-1">Enter how many magazines published</p>
                    </div>
                </div>

                <!-- ===================== NEWSLETTER ===================== -->
                <div class="bg-green-50 p-3 rounded">
                    <label class="font-semibold text-gray-700">Newsletter</label>
                    <select name="newsletter" id="newsletter" class="w-full border p-2 rounded mt-2" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>

                    <!-- Frequency box -->
                    <div id="news_freq_box" class="mt-2">
                        <input type="number" name="target_freq2" placeholder="Frequency per year"
                            class="w-full border p-2 rounded" min="0">
                        <p class="text-xs text-gray-500 mt-1">Target: 4 per year</p>
                    </div>

                    <!-- Number of newsletters (when No) -->
                    <div id="news_num_box" class="mt-2 hidden">
                        <input type="number" name="num_newsletter" placeholder="Number of Newsletters Published"
                            class="w-full border p-2 rounded" min="0">
                        <p class="text-xs text-gray-500 mt-1">Enter how many newsletters published</p>
                    </div>
                </div>

            </div>

            <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                <p class="text-sm font-semibold text-gray-700">Full marks (5) if:</p>
                <p class="text-xs text-gray-600 mt-1">Magazine frequency ≥ 1 AND Newsletter frequency ≥ 4</p>
            </div>

            <button class="bg-pink-600 text-white px-4 py-2 rounded w-full">
                Save Data & Calculate Marks
            </button>
        </form>

        <!-- ===================== DISPLAY SAVED MARKS ===================== -->
        <div id="marks-display-452" class="mt-6 p-4 bg-white rounded-lg border-2 border-pink-300">
            <h3 class="font-bold text-lg mb-2">Calculated Marks</h3>
            <div id="marks-content-452" class="text-center">
                <p class="text-gray-500">Loading...</p>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
            <div id="table-container-4.5.2"></div>
        </div>
    </div>

    <!-- ===================== SCRIPT ===================== -->
    <script>
    // ===================== Magazine Toggle =====================
    document.getElementById("magazine").addEventListener("change", function() {
        if (this.value === "No") {
            document.getElementById("mag_freq_box").classList.add("hidden");
            document.getElementById("mag_num_box").classList.remove("hidden");
        } else {
            document.getElementById("mag_freq_box").classList.remove("hidden");
            document.getElementById("mag_num_box").classList.add("hidden");
        }
    });

    // ===================== Newsletter Toggle =====================
    document.getElementById("newsletter").addEventListener("change", function() {
        if (this.value === "No") {
            document.getElementById("news_freq_box").classList.add("hidden");
            document.getElementById("news_num_box").classList.remove("hidden");
        } else {
            document.getElementById("news_freq_box").classList.remove("hidden");
            document.getElementById("news_num_box").classList.add("hidden");
        }
    });

    // ===================== Fetch Saved Marks =====================
    fetch('../backend/NBA/get_marks.php?criteria=4.5.2')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('marks-content-452');
            if (data.success) {
                container.innerHTML = `
                    <div class="text-4xl font-bold text-pink-600 mb-2">${parseFloat(data.marks).toFixed(2)} / 5</div>
                    <p class="text-gray-700 text-sm mt-2">Academic Year: ${data.academic_year}</p>
                `;
            } else {
                container.innerHTML = '<p class="text-gray-500">No data available yet.</p>';
            }
        })
        .catch(() => {
            document.getElementById('marks-content-452').innerHTML =
                '<p class="text-red-500">Error loading marks</p>';
        });

        loadTable('4.5.2', 'table-container-4.5.2', [
            { key: 'academic_year', label: 'Year' },
            { key: 'magazine', label: 'Magazine' },
            { key: 'target_freq1', label: 'Freq (Mag)' },
            { key: 'num_magazine', label: 'Num (Mag)' },
            { key: 'newsletter', label: 'Newsletter' },
            { key: 'target_freq2', label: 'Freq (News)' },
            { key: 'num_newsletter', label: 'Num (News)' },
            { key: 'marks', label: 'Marks' }
        ]);
    </script>

<?php } ?>


  <!-- ************************************
       4.5.3 - Student Participation in Events (10 marks)
  ************************************* -->
  <?php if ($title === "4.5.3 - Student Participation in Events (10)") { ?>
      <div class="p-6 border-l-4 border-orange-500 bg-orange-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-orange-800">4.5.3 - Student Participation in Events</h2>
          <p class="text-sm text-gray-700 mb-4">Track student participation and awards (4-year average)</p>

          <form method="post" action="../backend/NBA/save_453.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              
              <input type="number" name="total_participation" placeholder="Total Participation" class="w-full border p-2 rounded" required min="1">

              <div class="grid grid-cols-2 gap-4">
                  <div>
                      <label class="text-sm text-gray-600">Participation Within State</label>
                      <input type="number" name="participation_within_state" placeholder="Within state count" class="w-full border p-2 rounded" required min="0">
                      <p class="text-xs text-gray-500 mt-1">Target: 40% of total</p>
                  </div>
                  <div>
                      <label class="text-sm text-gray-600">Participation Outside State</label>
                      <input type="number" name="participation_outside_state" placeholder="Outside state count" class="w-full border p-2 rounded" required min="0">
                      <p class="text-xs text-gray-500 mt-1">Target: 20% of total</p>
                  </div>
              </div>

              <div>
                  <label class="text-sm text-gray-600">Awards Won</label>
                  <input type="number" name="awards" placeholder="Number of awards" class="w-full border p-2 rounded" required min="0">
                  <p class="text-xs text-gray-500 mt-1">Target: 5 awards</p>
              </div>

              <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                  <p class="text-sm font-semibold text-gray-700">Targets (4-year average):</p>
                  <p class="text-xs text-gray-600 mt-1">• Within state: 40% | Outside state: 20% | Awards: 5</p>
                  <p class="text-xs text-gray-600">Full 10 marks if all targets met or exceeded</p>
              </div>

              <button class="bg-orange-600 text-white px-4 py-2 rounded w-full">Save Data & Calculate Marks</button>
          </form>

          <div id="marks-display-453" class="mt-6 p-4 bg-white rounded-lg border-2 border-orange-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks (4-Year Average)</h3>
              <div id="marks-content-453" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>

          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-4.5.3"></div>
          </div>
      </div>

      <script>
      fetch('../backend/NBA/get_marks.php?criteria=4.5.3')
          .then(response => response.json())
          .then(data => {
              const container = document.getElementById('marks-content-453');
              if (data.success) {
                  let historyHTML = '';
                  if (data.history && data.history.length > 0) {
                      historyHTML = '<div class="mt-4 text-sm"><p class="font-semibold mb-2">Last 4 Years:</p><div class="grid grid-cols-1 gap-2">';
                      data.history.forEach(h => {
                          historyHTML += `
                              <div class="bg-gray-50 p-2 rounded text-xs">
                                  <p class="font-semibold">${h.academic_year}</p>
                                  <p>Total: ${h.total_participation} | Within: ${parseFloat(h.within_state_percentage).toFixed(1)}% | Outside: ${parseFloat(h.outside_state_percentage).toFixed(1)}% | Awards: ${h.awards}</p>
                              </div>
                          `;
                      });
                      historyHTML += '</div></div>';
                  }
                  
                  container.innerHTML = `
                      <div class="text-4xl font-bold text-orange-600 mb-2">${parseFloat(data.marks).toFixed(2)} / 10</div>
                      <div class="grid grid-cols-3 gap-3 mb-3">
                          <div class="bg-blue-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Within State</p>
                              <p class="text-lg font-bold text-blue-600">${parseFloat(data.within_state_percentage).toFixed(1)}%</p>
                              <p class="text-xs">${data.participation_within_state} students</p>
                          </div>
                          <div class="bg-green-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Outside State</p>
                              <p class="text-lg font-bold text-green-600">${parseFloat(data.outside_state_percentage).toFixed(1)}%</p>
                              <p class="text-xs">${data.participation_outside_state} students</p>
                          </div>
                          <div class="bg-purple-50 p-2 rounded">
                              <p class="text-xs text-gray-600">Awards</p>
                              <p class="text-lg font-bold text-purple-600">${data.awards}</p>
                          </div>
                      </div>
                      <p class="text-gray-700">Total Participation: ${data.total_participation}</p>
                      <p class="text-gray-700 text-sm mt-2">Academic Year: ${data.academic_year}</p>
                      ${historyHTML}
                  `;
              } else {
                  container.innerHTML = '<p class="text-gray-500">No data available yet.</p>';
              }
          })
          .catch(error => {
              document.getElementById('marks-content-453').innerHTML = '<p class="text-red-500">Error loading marks</p>';
          });

          loadTable('4.5.3', 'table-container-4.5.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'total_participation', label: 'Total Part.' },
              { key: 'participation_within_state', label: 'Within State' },
              { key: 'participation_outside_state', label: 'Outside State' },
              { key: 'within_state_percentage', label: 'Within %' },
              { key: 'outside_state_percentage', label: 'Outside %' },
              { key: 'awards', label: 'Awards' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 3.1 CO Attainment -->
  <?php if ($title === "3.1 - CO Attainment") { ?>
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
  <?php if ($title === "3.2 - PO Mapping") { ?>
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
  <?php if ($title === "4.3.1 - Placements") { ?>
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



  <!-- ************************************
       CRITERION 5 - Faculty Information
  ************************************* -->

  <!-- 5.1 -->
  <?php if (strpos($title, "5.1") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_51.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.1"></div>
          </div>
      </div>
      <script>
          loadTable('5.1', 'table-container-5.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.2 -->
  <?php if (strpos($title, "5.2") === 0) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_52.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.2"></div>
          </div>
      </div>
      <script>
          loadTable('5.2', 'table-container-5.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.3 -->
  <?php if (strpos($title, "5.3") === 0) { ?>
      <div class="p-6 border-l-4 border-purple-500 bg-purple-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-purple-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_53.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-purple-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.3"></div>
          </div>
      </div>
      <script>
          loadTable('5.3', 'table-container-5.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.4 -->
  <?php if (strpos($title, "5.4") === 0) { ?>
      <div class="p-6 border-l-4 border-teal-500 bg-teal-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-teal-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_54.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-teal-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.4"></div>
          </div>
      </div>
      <script>
          loadTable('5.4', 'table-container-5.4', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.5 -->
  <?php if (strpos($title, "5.5") === 0) { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_55.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.5"></div>
          </div>
      </div>
      <script>
          loadTable('5.5', 'table-container-5.5', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.6 -->
  <?php if (strpos($title, "5.6") === 0) { ?>
      <div class="p-6 border-l-4 border-pink-500 bg-pink-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-pink-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_56.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-pink-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.6"></div>
          </div>
      </div>
      <script>
          loadTable('5.6', 'table-container-5.6', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.7 -->
  <?php if (strpos($title, "5.7") === 0) { ?>
      <div class="p-6 border-l-4 border-orange-500 bg-orange-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-orange-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_57.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Details" class="w-full border p-2 rounded">
              <input type="number" name="value" placeholder="Value/Count" class="w-full border p-2 rounded">
              <button class="bg-orange-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.7"></div>
          </div>
      </div>
      <script>
          loadTable('5.7', 'table-container-5.7', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'value', label: 'Value' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.8 (General) -->
  <?php if ($title === "5.8 - Faculty Innovations") { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_58.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="innovation" placeholder="Innovation Details" class="w-full border p-2 rounded">
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.8"></div>
          </div>
      </div>
      <script>
          loadTable('5.8', 'table-container-5.8', [
              { key: 'academic_year', label: 'Year' },
              { key: 'innovation', label: 'Innovation' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.8.1 -->
  <?php if (strpos($title, "5.8.1") === 0) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_581.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Teaching Learning Process Details" class="w-full border p-2 rounded">
              <input type="number" name="marks" placeholder="Marks claimed" class="w-full border p-2 rounded">
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.8.1"></div>
          </div>
      </div>
      <script>
          loadTable('5.8.1', 'table-container-5.8.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.8.2 -->
  <?php if (strpos($title, "5.8.2") === 0) { ?>
      <div class="p-6 border-l-4 border-purple-500 bg-purple-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-purple-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_582.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Assessment & Evaluation Details" class="w-full border p-2 rounded">
              <input type="number" name="marks" placeholder="Marks claimed" class="w-full border p-2 rounded">
              <button class="bg-purple-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.8.2"></div>
          </div>
      </div>
      <script>
          loadTable('5.8.2', 'table-container-5.8.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.9 -->
  <?php if (strpos($title, "5.9") === 0) { ?>
      <div class="p-6 border-l-4 border-teal-500 bg-teal-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-teal-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_59.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="details" placeholder="Faculty Name" class="w-full border p-2 rounded">
              <input type="text" name="hours" placeholder="Hours" class="w-full border p-2 rounded">
              <button class="bg-teal-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.9"></div>
          </div>
      </div>
      <script>
          loadTable('5.9', 'table-container-5.9', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Name' },
              { key: 'hours', label: 'Hours' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.10 -->
  <?php if (strpos($title, "5.10") === 0) { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_510.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="topic" placeholder="Research Topic" class="w-full border p-2 rounded">
              <input type="text" name="publication" placeholder="Publication" class="w-full border p-2 rounded">
              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.10"></div>
          </div>
      </div>
      <script>
          loadTable('5.10', 'table-container-5.10', [
              { key: 'academic_year', label: 'Year' },
              { key: 'topic', label: 'Topic' },
              { key: 'publication', label: 'Publication' }
          ]);
      </script>
  <?php } ?>

</div>
</body>
</html>
