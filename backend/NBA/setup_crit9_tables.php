<?php
require_once __DIR__ . '/../db.php';

try {
    // 9.1 Mentoring System
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_91 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        mentoring_system TEXT,
        efficacy TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9.2 Feedback Analysis
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_92 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        feedback_process TEXT,
        corrective_measures TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9.3 Feedback on Facilities
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_93 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        facilities_feedback TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9.4 Self Learning
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_94 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        scope_details TEXT,
        facilities_materials TEXT,
        utilization_details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9.5 Career Guidance
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_95 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        career_guidance TEXT,
        counseling TEXT,
        training TEXT,
        placement_support TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9.6 Entrepreneurship Cell
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_96 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        initiatives TEXT,
        benefitted_students TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9.7 Co-curricular Activities
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_97 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        activities TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Tables for Criterion 9 created successfully.";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
