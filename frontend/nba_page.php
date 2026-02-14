<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
        else if (criteria === '5.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_51.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="num_students"]').value = row.num_students;
            targetForm.querySelector('[name="num_students"]').dispatchEvent(new Event('input'));
            targetForm.querySelector('[name="num_faculty"]').value = row.num_faculty;
            targetForm.querySelector('[name="num_faculty"]').dispatchEvent(new Event('input'));

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        
        // Criterion 1 Handlers
        else if (criteria === '1.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_11.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="vision"]').value = row.vision;
            targetForm.querySelector('[name="mission"]').value = row.mission;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '1.2') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_12.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="peo_title"]').value = row.peo_title;
            targetForm.querySelector('[name="peo_statement"]').value = row.peo_statement;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '1.3') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_13.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="process"]').value = row.process;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '1.4') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_14.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="process"]').value = row.process;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '1.5') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_15.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="peo_mission_matrix"]').value = row.peo_mission_matrix;
            targetForm.querySelector('[name="justification"]').value = row.justification;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }

        // Criterion 2 Handlers
        else if (['2.1.1', '2.1.2', '2.1.3', '2.1.4', '2.2.1', '2.2.2', '2.2.3', '2.2.4', '2.2.5'].includes(criteria)) {
            // Determine action script based on criteria
            let actionScript = '';
            if (criteria === '2.1.1') actionScript = '../backend/NBA/save_211.php';
            else if (criteria === '2.1.2') actionScript = '../backend/NBA/save_212.php';
            else if (criteria === '2.1.3') actionScript = '../backend/NBA/save_213.php';
            else if (criteria === '2.1.4') actionScript = '../backend/NBA/save_214.php';
            else if (criteria === '2.2.1') actionScript = '../backend/NBA/save_221.php';
            else if (criteria === '2.2.2') actionScript = '../backend/NBA/save_222.php';
            else if (criteria === '2.2.3') actionScript = '../backend/NBA/save_223.php';
            else if (criteria === '2.2.4') actionScript = '../backend/NBA/save_224.php';
            else if (criteria === '2.2.5') actionScript = '../backend/NBA/save_225.php';

            targetForm = document.querySelector(`form[action="${actionScript}"]`);
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="description"]').value = row.description;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }

        // Criterion 3 Handlers
        else if (criteria === '3.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_31.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="semester"]').value = row.semester;
            targetForm.querySelector('[name="subject_code"]').value = row.subject_code;
            targetForm.querySelector('[name="course_name"]').value = row.course_name;
            targetForm.querySelector('[name="cos_defined"]').value = row.cos_defined;
            targetForm.querySelector('[name="cos_embedded"]').value = row.cos_embedded;
            targetForm.querySelector('[name="articulation_matrix_co"]').value = row.articulation_matrix_co;
            targetForm.querySelector('[name="articulation_matrix_po"]').value = row.articulation_matrix_po;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '3.2.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_321.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="semester"]').value = row.semester;
            targetForm.querySelector('[name="assessment_tools"]').value = row.assessment_tools;
            targetForm.querySelector('[name="quality_relevance"]').value = row.quality_relevance;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '3.2.2') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_322.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="semester"]').value = row.semester;
            targetForm.querySelector('[name="subject_code"]').value = row.subject_code;
            targetForm.querySelector('[name="course_name"]').value = row.course_name;
            targetForm.querySelector('[name="attainment_level"]').value = row.attainment_level;
            targetForm.querySelector('[name="target_level"]').value = row.target_level;
            targetForm.querySelector('[name="observations"]').value = row.observations;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '3.3.1') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_331.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="semester"]').value = row.semester;
            targetForm.querySelector('[name="assessment_tools"]').value = row.assessment_tools;
            targetForm.querySelector('[name="quality_relevance"]').value = row.quality_relevance;

            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '3.3.2') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_332.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="semester"]').value = row.semester;
            targetForm.querySelector('[name="po_pso_name"]').value = row.po_pso_name;
            targetForm.querySelector('[name="target_level"]').value = row.target_level;
            targetForm.querySelector('[name="attainment_level"]').value = row.attainment_level;
            targetForm.querySelector('[name="observations"]').value = row.observations;

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
        // Criterion 5.2 Handler
        else if (criteria === '5.2') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_52.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="req_prof"]').value = row.req_prof;
            targetForm.querySelector('[name="avail_prof"]').value = row.avail_prof;
            targetForm.querySelector('[name="req_assoc"]').value = row.req_assoc;
            targetForm.querySelector('[name="avail_assoc"]').value = row.avail_assoc;
            targetForm.querySelector('[name="req_asst"]').value = row.req_asst;
            targetForm.querySelector('[name="avail_asst"]').value = row.avail_asst;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        // Criterion 5.3 Handler
        else if (criteria === '5.3') {
            targetForm = document.querySelector('form[action="../backend/NBA/save_53.php"]');
            targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
            targetForm.querySelector('[name="x_phd"]').value = row.x_phd;
            targetForm.querySelector('[name="y_mtech"]').value = row.y_mtech;
            targetForm.querySelector('[name="f_required"]').value = row.f_required;
            
            let idInput = targetForm.querySelector('[name="id"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        // Criterion 5.4 - 5.7 Handlers (Generic)
        else if (['5.4', '5.5', '5.6', '5.7'].includes(criteria)) {
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
        // Criterion 6.1 - 6.4 and 7.1 - 7.4 Handlers (Generic)
        else if (['6.1', '6.2', '6.3', '6.4', '7.1', '7.2', '7.3', '7.4'].includes(criteria)) {
            const cleanCrit = criteria.replace('.', ''); 
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
        
        // Criterion 8 Handlers
        else if (criteria === '8.1') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_81.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="regular_faculty"]').value = row.regular_faculty;
             targetForm.querySelector('[name="contract_faculty"]').value = row.contract_faculty;
             targetForm.querySelector('[name="student_count"]').value = row.student_count;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '8.2') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_82.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="x_phd"]').value = row.x_phd;
             targetForm.querySelector('[name="y_mtech"]').value = row.y_mtech;
             targetForm.querySelector('[name="rf_required"]').value = row.rf_required;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '8.3') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_83.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="mean_performance"]').value = row.mean_gpa_or_percentage;
             targetForm.querySelector('[name="students_appeared"]').value = row.students_appeared;
             targetForm.querySelector('[name="students_successful"]').value = row.students_successful;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '8.4.1') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_841.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="description"]').value = row.description;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '8.4.2') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_842.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="course_name"]').value = row.course_name;
             targetForm.querySelector('[name="attainment_level"]').value = row.attainment_level;
             targetForm.querySelector('[name="target_level"]').value = row.target_level;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '8.5.1') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_851.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="po_pso_name"]').value = row.po_pso_name;
             targetForm.querySelector('[name="attainment_level"]').value = row.attainment_level;
             targetForm.querySelector('[name="target_level"]').value = row.target_level;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '8.5.2') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_852.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="po_pso_name"]').value = row.po_pso_name;
             targetForm.querySelector('[name="action_taken"]').value = row.action_taken;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        // Criterion 9 Handlers
        else if (criteria === '9.1') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_91.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="mentoring_system"]').value = row.mentoring_system;
             targetForm.querySelector('[name="efficacy"]').value = row.efficacy;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '9.2') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_92.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="feedback_process"]').value = row.feedback_process;
             targetForm.querySelector('[name="corrective_measures"]').value = row.corrective_measures;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '9.3') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_93.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="facilities_feedback"]').value = row.facilities_feedback;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '9.4') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_94.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="scope_details"]').value = row.scope_details;
             targetForm.querySelector('[name="facilities_materials"]').value = row.facilities_materials;
             targetForm.querySelector('[name="utilization_details"]').value = row.utilization_details;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '9.5') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_95.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="career_guidance"]').value = row.career_guidance;
             targetForm.querySelector('[name="counseling"]').value = row.counseling;
             targetForm.querySelector('[name="training"]').value = row.training;
             targetForm.querySelector('[name="placement_support"]').value = row.placement_support;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '9.6') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_96.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="initiatives"]').value = row.initiatives;
             targetForm.querySelector('[name="benefitted_students"]').value = row.benefitted_students;
             
             let idInput = targetForm.querySelector('[name="id"]');
             if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                targetForm.appendChild(idInput);
            }
            idInput.value = row.id;
        }
        else if (criteria === '9.7') {
             targetForm = document.querySelector('form[action="../backend/NBA/save_97.php"]');
             targetForm.querySelector('[name="academic_year"]').value = row.academic_year;
             targetForm.querySelector('[name="activities"]').value = row.activities;
             
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

  <!-- CRITERION 1 FORMS -->
  <?php if (strpos($title, '1.1') !== false) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800">1.1 - Vision and Mission</h2>
          <form method="post" action="../backend/NBA/save_11.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="vision" placeholder="Vision Statement" class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="mission" placeholder="Mission Statement" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-1.1"></div>
          </div>
      </div>
      <script>
          loadTable('1.1', 'table-container-1.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'vision', label: 'Vision' },
              { key: 'mission', label: 'Mission' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '1.2') !== false) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800">1.2 - Program Educational Objectives (PEOs)</h2>
          <form method="post" action="../backend/NBA/save_12.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="peo_title" placeholder="PEO Title (e.g., PEO1)" class="w-full border p-2 rounded" required>
              <textarea name="peo_statement" placeholder="PEO Statement" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-1.2"></div>
          </div>
      </div>
      <script>
          loadTable('1.2', 'table-container-1.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'peo_title', label: 'Title' },
              { key: 'peo_statement', label: 'Statement' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '1.3') !== false) { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800">1.3 - Dissemination of Vision, Mission & PEOs</h2>
          <form method="post" action="../backend/NBA/save_13.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="process" placeholder="Describe the dissemination process..." class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-1.3"></div>
          </div>
      </div>
      <script>
          loadTable('1.3', 'table-container-1.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'process', label: 'Process Details' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '1.4') !== false) { ?>
      <div class="p-6 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-yellow-800">1.4 - Process for Defining Vision, Mission & PEOs</h2>
          <form method="post" action="../backend/NBA/save_14.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="process" placeholder="Describe the definition process..." class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-1.4"></div>
          </div>
      </div>
      <script>
          loadTable('1.4', 'table-container-1.4', [
              { key: 'academic_year', label: 'Year' },
              { key: 'process', label: 'Process Details' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '1.5') !== false) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800">1.5 - Consistency of PEOs with Mission</h2>
          <form method="post" action="../backend/NBA/save_15.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="peo_mission_matrix" placeholder="PEO-Mission Matrix (or description)" class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="justification" placeholder="Consistency Justification" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-1.5"></div>
          </div>
      </div>
      <script>
          loadTable('1.5', 'table-container-1.5', [
              { key: 'academic_year', label: 'Year' },
              { key: 'peo_mission_matrix', label: 'Matrix' },
              { key: 'justification', label: 'Justification' }
          ]);
      </script>
  <?php } ?>

  <!-- CRITERION 2 FORMS (2.1.1 - 2.2.5) -->
  <?php 
  $c2_forms = [
      '2.1.1' => ['title' => '2.1.1 - Process for Designing Curriculum', 'action' => '../backend/NBA/save_211.php', 'desc_placeholder' => 'Describe the process...'],
      '2.1.2' => ['title' => '2.1.2 - Structure of the Curriculum', 'action' => '../backend/NBA/save_212.php', 'desc_placeholder' => 'Describe the structure...'],
      '2.1.3' => ['title' => '2.1.3 - Components of the Curriculum', 'action' => '../backend/NBA/save_213.php', 'desc_placeholder' => 'List the components...'],
      '2.1.4' => ['title' => '2.1.4 - Process of Compliance', 'action' => '../backend/NBA/save_214.php', 'desc_placeholder' => 'Describe the compliance process...'],
      '2.2.1' => ['title' => '2.2.1 - Teaching Learning Process', 'action' => '../backend/NBA/save_221.php', 'desc_placeholder' => 'Describe the teaching learning process...'],
      '2.2.2' => ['title' => '2.2.2 - Quality of Exams/Assignments', 'action' => '../backend/NBA/save_222.php', 'desc_placeholder' => 'Describe quality assurance...'],
      '2.2.3' => ['title' => '2.2.3 - Student Projects', 'action' => '../backend/NBA/save_223.php', 'desc_placeholder' => 'Describe project allocation and evaluation...'],
      '2.2.4' => ['title' => '2.2.4 - Industry Interaction', 'action' => '../backend/NBA/save_224.php', 'desc_placeholder' => 'Describe industry initiatives...'],
      '2.2.5' => ['title' => '2.2.5 - Internships/Training', 'action' => '../backend/NBA/save_225.php', 'desc_placeholder' => 'Describe internship initiatives...']
  ];

  foreach ($c2_forms as $key => $form) {
      if (strpos($title, $key) !== false) { 
  ?>
      <div class="p-6 border-l-4 border-teal-500 bg-teal-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-teal-800"><?= $form['title'] ?></h2>
          <form method="post" action="<?= $form['action'] ?>" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="description" placeholder="<?= $form['desc_placeholder'] ?>" class="w-full border p-2 rounded h-40" required></textarea>
              <button class="bg-teal-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-<?= $key ?>"></div>
          </div>
      </div>
      <script>
          loadTable('<?= $key ?>', 'table-container-<?= $key ?>', [
              { key: 'academic_year', label: 'Year' },
              { key: 'description', label: 'Description' }
          ]);
      </script>
  <?php 
      }
  } 
  ?>

  <!-- CRITERION 3 FORMS -->
  <?php if (strpos($title, '3.1') !== false) { ?>
      <div class="p-6 border-l-4 border-purple-500 bg-purple-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-purple-800">3.1 - Establish the correlation between the courses and the POs & PSOs</h2>
          <form method="post" action="../backend/NBA/save_31.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="semester" placeholder="Semester (e.g., Odd 2024)" class="w-full border p-2 rounded" required>
              <input type="text" name="subject_code" placeholder="Subject Code" class="w-full border p-2 rounded" required>
              <input type="text" name="course_name" placeholder="Course Name" class="w-full border p-2 rounded" required>
              <textarea name="cos_defined" placeholder="Evidence of COs being defined for every course" class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="cos_embedded" placeholder="Availability of COs embedded in the syllabi" class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="articulation_matrix_co" placeholder="Explanation of Course Articulation Matrix table" class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="articulation_matrix_po" placeholder="Explanation of Program Articulation Matrix tables" class="w-full border p-2 rounded h-24" required></textarea>
              <button class="bg-purple-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-3.1"></div>
          </div>
      </div>
      <script>
          loadTable('3.1', 'table-container-3.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'semester', label: 'Semester' },
              { key: 'subject_code', label: 'Code' },
              { key: 'course_name', label: 'Course' },
              { key: 'cos_defined', label: 'COs Defined' },
              { key: 'cos_embedded', label: 'COs Embedded' },
              { key: 'articulation_matrix_co', label: 'Matrix CO' },
              { key: 'articulation_matrix_po', label: 'Matrix PO' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '3.2.1') !== false) { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800">3.2.1 - Assessment tools and processes (COs)</h2>
          <form method="post" action="../backend/NBA/save_321.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="semester" placeholder="Semester" class="w-full border p-2 rounded" required>
              <textarea name="assessment_tools" placeholder="List of assessment processes" class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="quality_relevance" placeholder="The quality/relevance of assessment processes & tools used" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-3.2.1"></div>
          </div>
      </div>
      <script>
          loadTable('3.2.1', 'table-container-3.2.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'semester', label: 'Semester' },
              { key: 'assessment_tools', label: 'Assessment Tools' },
              { key: 'quality_relevance', label: 'Quality & Relevance' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '3.2.2') !== false) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800">3.2.2 - Attainment of Course Outcomes</h2>
          <form method="post" action="../backend/NBA/save_322.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="semester" placeholder="Semester" class="w-full border p-2 rounded" required>
              <input type="text" name="subject_code" placeholder="Subject Code" class="w-full border p-2 rounded" required>
              <input type="text" name="course_name" placeholder="Course Name" class="w-full border p-2 rounded" required>
              <input type="text" name="target_level" placeholder="Target Level" class="w-full border p-2 rounded" required>
              <input type="text" name="attainment_level" placeholder="Attainment Level" class="w-full border p-2 rounded" required>
              <textarea name="observations" placeholder="Observations" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-3.2.2"></div>
          </div>
      </div>
      <script>
          loadTable('3.2.2', 'table-container-3.2.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'semester', label: 'Semester' },
              { key: 'subject_code', label: 'Code' },
              { key: 'course_name', label: 'Course' },
              { key: 'target_level', label: 'Target' },
              { key: 'attainment_level', label: 'Attainment' },
              { key: 'observations', label: 'Observations' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '3.3.1') !== false) { ?>
      <div class="p-6 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-yellow-800">3.3.1 - Assessment tools and processes (POs & PSOs)</h2>
          <form method="post" action="../backend/NBA/save_331.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="semester" placeholder="Semester" class="w-full border p-2 rounded" required>
              <textarea name="assessment_tools" placeholder="List of assessment tools & processes" class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="quality_relevance" placeholder="The quality/relevance of assessment tools/processes used" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-3.3.1"></div>
          </div>
      </div>
      <script>
          loadTable('3.3.1', 'table-container-3.3.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'semester', label: 'Semester' },
              { key: 'assessment_tools', label: 'Assessment Tools' },
              { key: 'quality_relevance', label: 'Quality & Relevance' }
          ]);
      </script>
  <?php } ?>

  <?php if (strpos($title, '3.3.2') !== false) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800">3.3.2 - Provide results of evaluation of each PO & PSO</h2>
          <form method="post" action="../backend/NBA/save_332.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="semester" placeholder="Semester" class="w-full border p-2 rounded" required>
              <input type="text" name="po_pso_name" placeholder="PO/PSO Name" class="w-full border p-2 rounded" required>
              <input type="text" name="target_level" placeholder="Target Level" class="w-full border p-2 rounded" required>
              <input type="text" name="attainment_level" placeholder="Attainment Level" class="w-full border p-2 rounded" required>
              <textarea name="observations" placeholder="Observations" class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-3.3.2"></div>
          </div>
      </div>
      <script>
          loadTable('3.3.2', 'table-container-3.3.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'semester', label: 'Semester' },
              { key: 'po_pso_name', label: 'PO/PSO' },
              { key: 'target_level', label: 'Target' },
              { key: 'attainment_level', label: 'Attainment' },
              { key: 'observations', label: 'Observations' }
          ]);
      </script>
  <?php } ?>

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
<?php if (strpos($title, '4.5.2') !== false) { ?>
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
  <?php if (strpos($title, '4.5.3') !== false) { ?>
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
          <p class="text-sm text-gray-700 mb-4">Student-Faculty Ratio (SFR)</p>

          <form method="post" action="../backend/NBA/save_51.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2023-24)" class="w-full border p-2 rounded" required>
              
              <div class="grid grid-cols-2 gap-4">
                  <div>
                      <label class="text-sm text-gray-600">Number of Students</label>
                      <input type="number" id="num_students" name="num_students" placeholder="Total Students" class="w-full border p-2 rounded" required min="0">
                  </div>
                  <div>
                      <label class="text-sm text-gray-600">Number of Faculty</label>
                      <input type="number" id="num_faculty" name="num_faculty" placeholder="Total Faculty" class="w-full border p-2 rounded" required min="1">
                  </div>
              </div>

              <div class="bg-blue-100 p-3 rounded">
                  <label class="font-semibold text-gray-700">Calculated SFR (Student/Faculty):</label>
                  <input type="text" id="sfr_display" class="w-full border p-2 rounded mt-1 bg-gray-100" readonly>
              </div>

              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>

          <!-- Display Marks / Average SFR -->
          <div id="marks-display-51" class="mt-6 p-4 bg-white rounded-lg border-2 border-blue-300">
              <h3 class="font-bold text-lg mb-2">Summary</h3>
              <p class="text-gray-600 text-sm mb-3">Average SFR (Last 3 Years)</p>
              <div id="marks-content-51" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>

          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.1"></div>
          </div>
      </div>

      <script>
          // Auto-calculate SFR
          function calculateSFR() {
              const s = parseInt(document.getElementById('num_students').value) || 0;
              const f = parseInt(document.getElementById('num_faculty').value) || 0;
              if (f > 0) {
                  document.getElementById('sfr_display').value = (s / f).toFixed(2);
              } else {
                  document.getElementById('sfr_display').value = '';
              }
          }
          document.getElementById('num_students').addEventListener('input', calculateSFR);
          document.getElementById('num_faculty').addEventListener('input', calculateSFR);

          // Fetch Marks/Summary
          fetch('../backend/NBA/get_marks.php?criteria=5.1')
              .then(response => response.json())
              .then(data => {
                  const container = document.getElementById('marks-content-51');
                  if (data.success) {
                      let historyHTML = '';
                      if (data.history && data.history.length > 0) {
                          historyHTML = '<div class="mt-4 text-sm"><p class="font-semibold mb-2">History:</p><div class="grid grid-cols-1 gap-2">';
                          data.history.forEach(h => {
                             historyHTML += `<div class="bg-gray-50 p-2 rounded flex justify-between"><span>${h.academic_year}</span> <span>SFR: ${parseFloat(h.sfr).toFixed(2)}</span></div>`; 
                          });
                          historyHTML += '</div></div>';
                      }

                      container.innerHTML = `
                          <div class="text-4xl font-bold text-blue-600 mb-2">${parseFloat(data.avg_sfr).toFixed(2)}</div>
                          <p class="text-gray-700">Latest SFR: ${parseFloat(data.sfr).toFixed(2)}</p>
                          <p class="text-gray-700 text-sm mt-2">Academic Year: ${data.academic_year}</p>
                          ${historyHTML}
                      `;
                  } else {
                      container.innerHTML = '<p class="text-gray-500">No data available yet.</p>';
                  }
              })
              .catch(e => console.error(e));

          loadTable('5.1', 'table-container-5.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'num_students', label: 'Students' },
              { key: 'num_faculty', label: 'Faculty' },
              { key: 'sfr', label: 'SFR', format: v => parseFloat(v).toFixed(2) },
              { key: 'avg_sfr', label: 'Avg SFR', format: v => parseFloat(v).toFixed(2) }
          ]);
      </script>
  <?php } ?>

  <!-- 5.2 -->
  <!-- 5.2 -->
  <?php if (strpos($title, "5.2") === 0) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Faculty Cadre Proportion (Professors, Associate Professors, Assistant Professors)</p>

          <form method="post" action="../backend/NBA/save_52.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., CAY 2024-25)" class="w-full border p-2 rounded" required>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Professors -->
                  <div class="bg-blue-100 p-3 rounded">
                      <h3 class="font-semibold text-blue-800 mb-2">Professors (F1)</h3>
                      <input type="number" name="req_prof" placeholder="Required (RF1)" class="w-full border p-2 rounded mb-2" required min="0">
                      <input type="number" name="avail_prof" placeholder="Available (AF1)" class="w-full border p-2 rounded" required min="0">
                  </div>
                  
                  <!-- Associate Professors -->
                  <div class="bg-indigo-100 p-3 rounded">
                      <h3 class="font-semibold text-indigo-800 mb-2">Associate Prof (F2)</h3>
                      <input type="number" name="req_assoc" placeholder="Required (RF2)" class="w-full border p-2 rounded mb-2" required min="0">
                      <input type="number" name="avail_assoc" placeholder="Available (AF2)" class="w-full border p-2 rounded" required min="0">
                  </div>
                  
                  <!-- Assistant Professors -->
                  <div class="bg-purple-100 p-3 rounded">
                      <h3 class="font-semibold text-purple-800 mb-2">Assistant Prof (F3)</h3>
                      <input type="number" name="req_asst" placeholder="Required (RF3)" class="w-full border p-2 rounded mb-2" required min="0">
                      <input type="number" name="avail_asst" placeholder="Available (AF3)" class="w-full border p-2 rounded" required min="0">
                  </div>
              </div>

              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>

          <!-- Display Marks / Summary -->
          <div id="marks-display-52" class="mt-6 p-4 bg-white rounded-lg border-2 border-green-300">
              <h3 class="font-bold text-lg mb-2">Calculated Marks (3 Years Average)</h3>
              <div id="marks-content-52" class="text-center">
                  <p class="text-gray-500">Loading...</p>
              </div>
          </div>

          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.2"></div>
          </div>
      </div>
      <script>
          // Fetch Marks/Summary
          fetch('../backend/NBA/get_marks.php?criteria=5.2')
              .then(response => response.json())
              .then(data => {
                  const container = document.getElementById('marks-content-52');
                  if (data.success) {
                      let historyHTML = '';
                      if (data.history && data.history.length > 0) {
                          historyHTML = '<div class="mt-4 overflow-x-auto"><table class="w-full text-sm text-left"><thead><tr class="bg-gray-100"><th>Year</th><th>Prof (Req/Av)</th><th>Assoc (Req/Av)</th><th>Asst (Req/Av)</th></tr></thead><tbody>';
                          data.history.forEach(h => {
                             historyHTML += `<tr>
                                <td class="p-2 border">${h.academic_year}</td>
                                <td class="p-2 border">${h.req_prof} / ${h.avail_prof}</td>
                                <td class="p-2 border">${h.req_assoc} / ${h.avail_assoc}</td>
                                <td class="p-2 border">${h.req_asst} / ${h.avail_asst}</td>
                             </tr>`; 
                          });
                          historyHTML += '</tbody></table></div>';
                          
                          // Add Averages row
                          historyHTML += `<div class="mt-2 text-sm bg-yellow-50 p-2 rounded">
                            <p class="font-semibold">Averages:</p>
                            <div class="grid grid-cols-3 gap-2">
                                <div><span class="font-bold">RF1:</span> ${parseFloat(data.avg_rf1).toFixed(2)} <span class="font-bold">AF1:</span> ${parseFloat(data.avg_af1).toFixed(2)}</div>
                                <div><span class="font-bold">RF2:</span> ${parseFloat(data.avg_rf2).toFixed(2)} <span class="font-bold">AF2:</span> ${parseFloat(data.avg_af2).toFixed(2)}</div>
                                <div><span class="font-bold">RF3:</span> ${parseFloat(data.avg_rf3).toFixed(2)} <span class="font-bold">AF3:</span> ${parseFloat(data.avg_af3).toFixed(2)}</div>
                            </div>
                            <div class="mt-2">
                                <p><span class="font-bold">Cadre Ratio Marks:</span> (${parseFloat(data.r1).toFixed(2)} + ${parseFloat(data.r2).toFixed(2)}*0.6 + ${parseFloat(data.r3).toFixed(2)}*0.4) * 10 = <span class="text-xl font-bold text-green-600">${parseFloat(data.cadre_marks).toFixed(2)}</span></p>
                            </div>
                          </div>`;
                      }

                      container.innerHTML = `
                          ${historyHTML}
                      `;
                  } else {
                      container.innerHTML = '<p class="text-gray-500">No data available yet.</p>';
                  }
              })
              .catch(e => console.error(e));

          loadTable('5.2', 'table-container-5.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'req_prof', label: 'Req Prof' },
              { key: 'avail_prof', label: 'Avail Prof' },
              { key: 'req_assoc', label: 'Req Assoc' },
              { key: 'avail_assoc', label: 'Avail Assoc' },
              { key: 'req_asst', label: 'Req Asst' },
              { key: 'avail_asst', label: 'Avail Asst' }
          ]);
      </script>
  <?php } ?>

  <!-- 5.3 -->
  <!-- 5.3 -->
  <?php if (strpos($title, "5.3") === 0) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Faculty Qualification (FQ = 2.0 x [(10X + 4Y)/F])</p>

          <form method="post" action="../backend/NBA/save_53.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year (e.g., CAY 2024-25)" class="w-full border p-2 rounded" required>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                      <label class="block text-sm font-semibold mb-1">Faculty with Ph.D (X)</label>
                      <input type="number" name="x_phd" placeholder="X" class="w-full border p-2 rounded" required min="0">
                  </div>
                  <div>
                      <label class="block text-sm font-semibold mb-1">Faculty with M.Tech (Y)</label>
                      <input type="number" name="y_mtech" placeholder="Y" class="w-full border p-2 rounded" required min="0">
                  </div>
                  <div>
                      <label class="block text-sm font-semibold mb-1">Required Faculty (F)</label>
                      <input type="number" name="f_required" placeholder="F" class="w-full border p-2 rounded" required min="1">
                  </div>
              </div>
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>

          <!-- Marks -->
          <div id="marks-display-53" class="mt-6 p-4 bg-white rounded-lg border-2 border-green-300">
               <h3 class="font-bold text-lg mb-2 text-center">Average Assessment (3 Years)</h3>
               <div id="marks-content-53" class="text-center">
                   <p class="text-gray-500">Loading...</p>
               </div>
          </div>

          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-5.3"></div>
          </div>
      </div>
      <script>
          fetch('../backend/NBA/get_marks.php?criteria=5.3')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('marks-content-53');
                if (data.success) {
                    let historyHTML = '';
                    if (data.history && data.history.length > 0) {
                         historyHTML = '<div class="mt-4 overflow-x-auto"><table class="w-full text-sm text-center border"><thead><tr class="bg-gray-100"><th>Year</th><th>X</th><th>Y</th><th>F</th><th>FQ = 2.0*[(10X+4Y)/F]</th></tr></thead><tbody>';
                         data.history.forEach(h => {
                             historyHTML += `<tr>
                                <td class="p-2 border">${h.academic_year}</td>
                                <td class="p-2 border">${h.x_phd}</td>
                                <td class="p-2 border">${h.y_mtech}</td>
                                <td class="p-2 border">${h.f_required}</td>
                                <td class="p-2 border font-bold">${parseFloat(h.fq_score).toFixed(2)}</td>
                             </tr>`;
                         });
                         historyHTML += '</tbody></table></div>';
                    }
                    
                    container.innerHTML = `
                         ${historyHTML}
                         <div class="mt-4 p-2 bg-yellow-50 rounded">
                             <p class="text-lg font-bold text-green-700">Average FQ Score: ${parseFloat(data.avg_assessment).toFixed(2)}</p>
                         </div>
                    `;
                } else {
                    container.innerHTML = '<p class="text-gray-500">No data available yet.</p>';
                }
            });

          loadTable('5.3', 'table-container-5.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'x_phd', label: 'X (Ph.D)' },
              { key: 'y_mtech', label: 'Y (M.Tech)' },
              { key: 'f_required', label: 'F (Regular)' },
              { key: 'fq_score', label: 'FQ Score' }
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
  
  <!-- ************************************
       Criterion 6 - Infrastructure & Facilities
  ************************************* -->

  <!-- 6.1 -->
  <?php if (strpos($title, "6.1") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_61.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding adequate and well equipped laboratories, and technical manpower" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 40)" class="w-full border p-2 rounded">
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-6.1"></div>
          </div>
      </div>
      <script>
          loadTable('6.1', 'table-container-6.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 6.2 -->
  <?php if (strpos($title, "6.2") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_62.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding Laboratories maintenance and overall ambience" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 10)" class="w-full border p-2 rounded">
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-6.2"></div>
          </div>
      </div>
      <script>
          loadTable('6.2', 'table-container-6.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 6.3 -->
  <?php if (strpos($title, "6.3") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_63.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding Safety measures in laboratories" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 10)" class="w-full border p-2 rounded">
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-6.3"></div>
          </div>
      </div>
      <script>
          loadTable('6.3', 'table-container-6.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 6.4 -->
  <?php if (strpos($title, "6.4") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_64.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding Project laboratory/Faculties" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 20)" class="w-full border p-2 rounded">
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-6.4"></div>
          </div>
      </div>
      <script>
          loadTable('6.4', 'table-container-6.4', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- ************************************
       Criterion 7 - Continuous Improvement
  ************************************* -->

  <!-- 7.1 -->
  <?php if (strpos($title, "7.1") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_71.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding POs and PSOs assessment actions" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 30)" class="w-full border p-2 rounded">
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-7.1"></div>
          </div>
      </div>
      <script>
          loadTable('7.1', 'table-container-7.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 7.2 -->
  <?php if (strpos($title, "7.2") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_72.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding Academic Audit actions" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 15)" class="w-full border p-2 rounded">
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-7.2"></div>
          </div>
      </div>
      <script>
          loadTable('7.2', 'table-container-7.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 7.3 -->
  <?php if (strpos($title, "7.3") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_73.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding Improvement in placement, Higher Studies and Entrepreneurship" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 10)" class="w-full border p-2 rounded">
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-7.3"></div>
          </div>
      </div>
      <script>
          loadTable('7.3', 'table-container-7.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>

  <!-- 7.4 -->
  <?php if (strpos($title, "7.4") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <form method="post" action="../backend/NBA/save_74.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="details" placeholder="Enter details regarding Improvement in quality of students admitted" class="w-full border p-2 rounded h-32"></textarea>
              <input type="number" step="0.01" name="marks" placeholder="Marks claimed (Max 20)" class="w-full border p-2 rounded">
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-7.4"></div>
          </div>
      </div>
      <script>
          loadTable('7.4', 'table-container-7.4', [
              { key: 'academic_year', label: 'Year' },
              { key: 'details', label: 'Details' },
              { key: 'marks', label: 'Marks' }
          ]);
      </script>
  <?php } ?>


  <!-- ************************************
       Criterion 8 - First Year Academics
  ************************************* -->

  <!-- 8.1 -->
  <?php if (strpos($title, "8.1") === 0) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">First Year Student-Faculty Ratio (FYSFR)</p>
          <form method="post" action="../backend/NBA/save_81.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="number" name="regular_faculty" placeholder="Number of Regular Faculty" class="w-full border p-2 rounded" required>
              <input type="number" name="contract_faculty" placeholder="Number of Contract Faculty" class="w-full border p-2 rounded" required>
              <input type="number" name="student_count" placeholder="Number of Students" class="w-full border p-2 rounded" required>
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.1"></div>
          </div>
      </div>
      <script>
          loadTable('8.1', 'table-container-8.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'regular_faculty', label: 'Regular Faculty' },
              { key: 'contract_faculty', label: 'Contract Faculty' },
              { key: 'student_count', label: 'Student Count' },
              { key: 'fysfr', label: 'FYSFR' },
              { key: 'assessment_score', label: 'Assessment' }
          ]);
      </script>
  <?php } ?>

  <!-- 8.2 -->
  <?php if (strpos($title, "8.2") === 0) { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Qualification of Faculty Teaching First Year Common Courses</p>
          <form method="post" action="../backend/NBA/save_82.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="number" name="x_phd" placeholder="Faculty with Ph.D (x)" class="w-full border p-2 rounded" required>
              <input type="number" name="y_mtech" placeholder="Faculty with M.Tech/M.Sc/M.Phil (y)" class="w-full border p-2 rounded" required>
              <input type="number" name="rf_required" placeholder="Required Faculty (RF)" class="w-full border p-2 rounded" required>
              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.2"></div>
          </div>
      </div>
      <script>
          loadTable('8.2', 'table-container-8.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'x_phd', label: 'Ph.D (x)' },
              { key: 'y_mtech', label: 'M.Tech (y)' },
              { key: 'rf_required', label: 'RF' },
              { key: 'assessment_score', label: 'Assessment' }
          ]);
      </script>
  <?php } ?>

  <!-- 8.3 -->
  <?php if (strpos($title, "8.3") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">First Year Academic Performance</p>
          <form method="post" action="../backend/NBA/save_83.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="number" step="0.01" name="mean_performance" placeholder="Mean GPA * 10 OR Mean Percentage" class="w-full border p-2 rounded" required>
              <input type="number" name="students_appeared" placeholder="Number of Students Appeared" class="w-full border p-2 rounded" required>
              <input type="number" name="students_successful" placeholder="Number of Successful Students" class="w-full border p-2 rounded" required>
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.3"></div>
          </div>
      </div>
      <script>
          loadTable('8.3', 'table-container-8.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'mean_gpa_or_percentage', label: 'Mean Perf.' },
              { key: 'students_appeared', label: 'Appeared' },
              { key: 'students_successful', label: 'Successful' },
              { key: 'api_score', label: 'API Score' }
          ]);
      </script>
  <?php } ?>

  <!-- 8.4.1 -->
  <?php if (strpos($title, "8.4.1") === 0) { ?>
      <div class="p-6 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-yellow-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Description of Assessment Processes</p>
          <form method="post" action="../backend/NBA/save_841.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="description" placeholder="Describe the assessment processes..." class="w-full border p-2 rounded h-32"></textarea>
              <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.4.1"></div>
          </div>
      </div>
      <script>
          loadTable('8.4.1', 'table-container-8.4.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'description', label: 'Description' }
          ]);
      </script>
  <?php } ?>

  <!-- 8.4.2 -->
  <?php if (strpos($title, "8.4.2") === 0) { ?>
      <div class="p-6 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-yellow-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Attainment of Course Outcomes</p>
          <form method="post" action="../backend/NBA/save_842.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="course_name" placeholder="Course Name" class="w-full border p-2 rounded" required>
              <input type="number" step="0.01" name="attainment_level" placeholder="Attainment Level" class="w-full border p-2 rounded" required>
              <input type="number" step="0.01" name="target_level" placeholder="Target Level" class="w-full border p-2 rounded" required>
              <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.4.2"></div>
          </div>
      </div>
      <script>
          loadTable('8.4.2', 'table-container-8.4.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'course_name', label: 'Course' },
              { key: 'attainment_level', label: 'Attainment' },
              { key: 'target_level', label: 'Target' }
          ]);
      </script>
  <?php } ?>

  <!-- 8.5.1 -->
  <?php if (strpos($title, "8.5.1") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Evaluation of POs/PSOs</p>
          <form method="post" action="../backend/NBA/save_851.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="po_pso_name" placeholder="PO/PSO Name" class="w-full border p-2 rounded" required>
              <input type="number" step="0.01" name="attainment_level" placeholder="Attainment Level" class="w-full border p-2 rounded" required>
              <input type="number" step="0.01" name="target_level" placeholder="Target Level" class="w-full border p-2 rounded" required>
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.5.1"></div>
          </div>
      </div>
      <script>
          loadTable('8.5.1', 'table-container-8.5.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'po_pso_name', label: 'PO/PSO' },
              { key: 'attainment_level', label: 'Attainment' },
              { key: 'target_level', label: 'Target' }
          ]);
      </script>
  <?php } ?>

  <!-- 8.5.2 -->
  <?php if (strpos($title, "8.5.2") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Actions Taken based on PO/PSO Evaluation</p>
          <form method="post" action="../backend/NBA/save_852.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <input type="text" name="po_pso_name" placeholder="PO/PSO Name" class="w-full border p-2 rounded" required>
              <textarea name="action_taken" placeholder="Actions Taken" class="w-full border p-2 rounded h-32"></textarea>
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-8.5.2"></div>
          </div>
      </div>
      <script>
          loadTable('8.5.2', 'table-container-8.5.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'po_pso_name', label: 'PO/PSO' },
              { key: 'action_taken', label: 'Action Taken' }
          ]);
      </script>
  <?php } ?>



  <!-- ************************************
       Criterion 9 - Student Support Systems
  ************************************* -->

  <!-- 9.1 -->
  <?php if (strpos($title, "9.1") === 0) { ?>
      <div class="p-6 border-l-4 border-green-500 bg-green-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-green-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Mentoring system to help at individual level</p>
          <form method="post" action="../backend/NBA/save_91.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="mentoring_system" placeholder="Details of the mentoring system..." class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="efficacy" placeholder="Efficacy of the system..." class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.1"></div>
          </div>
      </div>
      <script>
          loadTable('9.1', 'table-container-9.1', [
              { key: 'academic_year', label: 'Year' },
              { key: 'mentoring_system', label: 'Mentoring Details' },
              { key: 'efficacy', label: 'Efficacy' }
          ]);
      </script>
  <?php } ?>

  <!-- 9.2 -->
  <?php if (strpos($title, "9.2") === 0) { ?>
      <div class="p-6 border-l-4 border-indigo-500 bg-indigo-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-indigo-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Feedback analysis and reward /corrective measures taken</p>
          <form method="post" action="../backend/NBA/save_92.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="feedback_process" placeholder="Methodology for feedback analysis..." class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="corrective_measures" placeholder="Corrective measures taken..." class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.2"></div>
          </div>
      </div>
      <script>
          loadTable('9.2', 'table-container-9.2', [
              { key: 'academic_year', label: 'Year' },
              { key: 'feedback_process', label: 'Feedback Process' },
              { key: 'corrective_measures', label: 'Corrective Measures' }
          ]);
      </script>
  <?php } ?>

  <!-- 9.3 -->
  <?php if (strpos($title, "9.3") === 0) { ?>
      <div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-blue-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Feedback on facilities</p>
          <form method="post" action="../backend/NBA/save_93.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="facilities_feedback" placeholder="Feedback collection, analysis and corrective action..." class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.3"></div>
          </div>
      </div>
      <script>
          loadTable('9.3', 'table-container-9.3', [
              { key: 'academic_year', label: 'Year' },
              { key: 'facilities_feedback', label: 'Details' }
          ]);
      </script>
  <?php } ?>

  <!-- 9.4 -->
  <?php if (strpos($title, "9.4") === 0) { ?>
      <div class="p-6 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-yellow-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Self - Learning</p>
          <form method="post" action="../backend/NBA/save_94.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="scope_details" placeholder="Scope for self-learning..." class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="facilities_materials" placeholder="Facilities & materials (Webinars, Podcast, MOOCs etc)..." class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="utilization_details" placeholder="Effective utilization details..." class="w-full border p-2 rounded h-24" required></textarea>
              <button class="bg-yellow-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.4"></div>
          </div>
      </div>
      <script>
          loadTable('9.4', 'table-container-9.4', [
              { key: 'academic_year', label: 'Year' },
              { key: 'scope_details', label: 'Scope' },
              { key: 'facilities_materials', label: 'Facilities' },
              { key: 'utilization_details', label: 'Utilization' }
          ]);
      </script>
  <?php } ?>

  <!-- 9.5 -->
  <?php if (strpos($title, "9.5") === 0) { ?>
      <div class="p-6 border-l-4 border-red-500 bg-red-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-red-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Career Guidance, Training, Placement</p>
          <form method="post" action="../backend/NBA/save_95.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="career_guidance" placeholder="Availability of career guidance facilities..." class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="counseling" placeholder="Counseling for higher studies (GATE/GRE, GMAT, etc.)..." class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="training" placeholder="Pre-placement training..." class="w-full border p-2 rounded h-24" required></textarea>
              <textarea name="placement_support" placeholder="Placement process and support..." class="w-full border p-2 rounded h-24" required></textarea>
              <button class="bg-red-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.5"></div>
          </div>
      </div>
      <script>
          loadTable('9.5', 'table-container-9.5', [
              { key: 'academic_year', label: 'Year' },
              { key: 'career_guidance', label: 'Guidance' },
              { key: 'counseling', label: 'Counseling' },
              { key: 'training', label: 'Training' },
              { key: 'placement_support', label: 'Placement' }
          ]);
      </script>
  <?php } ?>

  <!-- 9.6 -->
  <?php if (strpos($title, "9.6") === 0) { ?>
      <div class="p-6 border-l-4 border-teal-500 bg-teal-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-teal-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Entrepreneurship Cell</p>
          <form method="post" action="../backend/NBA/save_96.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="initiatives" placeholder="Entrepreneurship initiatives..." class="w-full border p-2 rounded h-32" required></textarea>
              <textarea name="benefitted_students" placeholder="Data on students benefitted..." class="w-full border p-2 rounded h-32" required></textarea>
              <button class="bg-teal-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.6"></div>
          </div>
      </div>
      <script>
          loadTable('9.6', 'table-container-9.6', [
              { key: 'academic_year', label: 'Year' },
              { key: 'initiatives', label: 'Initiatives' },
              { key: 'benefitted_students', label: 'Benefitted Students' }
          ]);
      </script>
  <?php } ?>

  <!-- 9.7 -->
  <?php if (strpos($title, "9.7") === 0) { ?>
      <div class="p-6 border-l-4 border-purple-500 bg-purple-50 rounded-lg mb-6">
          <h2 class="text-xl font-bold mb-4 text-purple-800"><?= h($title) ?></h2>
          <p class="text-sm text-gray-700 mb-4">Co-curricular and Extra-curricular Activities</p>
          <form method="post" action="../backend/NBA/save_97.php" class="space-y-4">
              <input type="text" name="academic_year" placeholder="Academic Year" class="w-full border p-2 rounded" required>
              <textarea name="activities" placeholder="Details of sports, cultural facilities, NCC, NSS, clubs, and annual events..." class="w-full border p-2 rounded h-40" required></textarea>
              <button class="bg-purple-600 text-white px-4 py-2 rounded w-full">Save Data</button>
          </form>
          <div class="mt-8">
              <h3 class="font-bold text-lg text-gray-700">Saved Records</h3>
              <div id="table-container-9.7"></div>
          </div>
      </div>
      <script>
          loadTable('9.7', 'table-container-9.7', [
              { key: 'academic_year', label: 'Year' },
              { key: 'activities', label: 'Activities Details' }
          ]);
      </script>
  <?php } ?>

</div>
</body>
</html>


