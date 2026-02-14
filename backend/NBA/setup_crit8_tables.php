<?php
require_once '../db.php';

try {
    // 8.1 First Year Student-Faculty Ratio (FYSFR)
    $sql81 = "CREATE TABLE IF NOT EXISTS nba_criterion_81 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        regular_faculty INT NOT NULL,
        contract_faculty INT NOT NULL,
        student_count INT NOT NULL,
        fysfr FLOAT NOT NULL,
        assessment_score FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql81);
    echo "Table 'nba_criterion_81' created.<br>";

    // 8.2 Qualification of Faculty Teaching First Year Common Courses
    $sql82 = "CREATE TABLE IF NOT EXISTS nba_criterion_82 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        x_phd INT NOT NULL,
        y_mtech INT NOT NULL,
        rf_required INT NOT NULL,
        assessment_score FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql82);
    echo "Table 'nba_criterion_82' created.<br>";

    // 8.3 First Year Academic Performance
    $sql83 = "CREATE TABLE IF NOT EXISTS nba_criterion_83 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        mean_gpa_or_percentage FLOAT NOT NULL,
        students_appeared INT NOT NULL,
        students_successful INT NOT NULL,
        api_score FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql83);
    echo "Table 'nba_criterion_83' created.<br>";

    // 8.4.1 Describe the assessment processes
    $sql841 = "CREATE TABLE IF NOT EXISTS nba_criterion_841 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql841);
    echo "Table 'nba_criterion_841' created.<br>";

    // 8.4.2 Record the attainment of Course Outcomes
    $sql842 = "CREATE TABLE IF NOT EXISTS nba_criterion_842 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        course_name VARCHAR(100) NOT NULL,
        attainment_level FLOAT NOT NULL,
        target_level FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql842);
    echo "Table 'nba_criterion_842' created.<br>";

    // 8.5.1 Indicate results of evaluation of each relevant PO/PSO
    $sql851 = "CREATE TABLE IF NOT EXISTS nba_criterion_851 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        po_pso_name VARCHAR(50) NOT NULL,
        attainment_level FLOAT NOT NULL,
        target_level FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql851);
    echo "Table 'nba_criterion_851' created.<br>";

    // 8.5.2 Actions taken based on the results of evaluation of relevant POs/PSOs
    $sql852 = "CREATE TABLE IF NOT EXISTS nba_criterion_852 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        po_pso_name VARCHAR(50) NOT NULL,
        action_taken TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql852);
    echo "Table 'nba_criterion_852' created.<br>";

    echo "All Criterion 8 tables setup successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>
