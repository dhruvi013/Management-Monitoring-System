<?php
require_once __DIR__ . '/../db.php';

try {
    // 10.1 Organization, Governance and Transparency
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_10_1 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        strategic_plan TEXT,
        admin_setup TEXT,
        decentralization TEXT,
        faculty_participation TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 10.2 Budget Allocation, Utilization, and Public Accounting at Institute level
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_10_2 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        budget_adequacy TEXT,
        utilization TEXT,
        audited_statements_link VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 10.3 Program Specific Budget Allocation, Utilization
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_10_3 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        budget_adequacy TEXT,
        utilization TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 10.4 Library and Internet
    $pdo->exec("CREATE TABLE IF NOT EXISTS nba_criterion_10_4 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(20) NOT NULL,
        library_details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Tables for Criterion 10 created successfully.";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
