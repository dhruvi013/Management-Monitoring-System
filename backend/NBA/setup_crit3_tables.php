<?php
require_once '../db.php';

try {
    // Table for 3.1: Correlation between courses and POs & PSOs
    $sql31 = "CREATE TABLE IF NOT EXISTS nba_criterion_31 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        course_name VARCHAR(255) NOT NULL,
        cos_defined TEXT,
        cos_embedded TEXT,
        articulation_matrix_co TEXT,
        articulation_matrix_po TEXT
    )";
    $pdo->exec($sql31);
    echo "Table 'nba_criterion_31' created or already exists.<br>";

    // Table for 3.2.1: Assessment tools and processes (COs)
    $sql321 = "CREATE TABLE IF NOT EXISTS nba_criterion_321 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        assessment_tools TEXT,
        quality_relevance TEXT
    )";
    $pdo->exec($sql321);
    echo "Table 'nba_criterion_321' created or already exists.<br>";

    // Table for 3.2.2: Attainment of Course Outcomes
    $sql322 = "CREATE TABLE IF NOT EXISTS nba_criterion_322 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        course_name VARCHAR(255) NOT NULL,
        attainment_level VARCHAR(50),
        target_level VARCHAR(50),
        observations TEXT
    )";
    $pdo->exec($sql322);
    echo "Table 'nba_criterion_322' created or already exists.<br>";

    // Table for 3.3.1: Assessment tools and processes (POs & PSOs)
    $sql331 = "CREATE TABLE IF NOT EXISTS nba_criterion_331 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        assessment_tools TEXT,
        quality_relevance TEXT
    )";
    $pdo->exec($sql331);
    echo "Table 'nba_criterion_331' created or already exists.<br>";

    // Table for 3.3.2: Attainment of Program Outcomes and Program Specific Outcomes
    $sql332 = "CREATE TABLE IF NOT EXISTS nba_criterion_332 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        po_pso_name VARCHAR(50) NOT NULL,
        target_level VARCHAR(50),
        attainment_level VARCHAR(50),
        observations TEXT
    )";
    $pdo->exec($sql332);
    echo "Table 'nba_criterion_332' created or already exists.<br>";

    echo "All Criterion 3 tables setup successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>
