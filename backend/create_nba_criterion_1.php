<?php
require_once __DIR__ . '/db.php';

try {
    $sql = "
    -- 1.1 Vision & Mission
    CREATE TABLE IF NOT EXISTS nba_criterion_11 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        vision TEXT NOT NULL,
        mission TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 1.2 PEOs
    CREATE TABLE IF NOT EXISTS nba_criterion_12 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        peo_title VARCHAR(255) NOT NULL, -- e.g., PEO1
        peo_statement TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 1.3 Dissemination
    CREATE TABLE IF NOT EXISTS nba_criterion_13 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        process TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 1.4 Process for Definition
    CREATE TABLE IF NOT EXISTS nba_criterion_14 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        process TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 1.5 Consistency (Matrix & Justification)
    CREATE TABLE IF NOT EXISTS nba_criterion_15 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        peo_mission_matrix TEXT NOT NULL, -- JSON or Text representation
        justification TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $pdo->exec($sql);
    echo "Tables for Criterion 1 created successfully.";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
