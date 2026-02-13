<?php
require_once __DIR__ . '/db.php';

try {
    $sql = "
    -- 2.1.1 Process for designing curriculum (10)
    CREATE TABLE IF NOT EXISTS nba_criterion_211 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.1.2 Structure of the Curriculum (5)
    CREATE TABLE IF NOT EXISTS nba_criterion_212 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.1.3 Components of the curriculum (5)
    CREATE TABLE IF NOT EXISTS nba_criterion_213 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.1.4 Process of compliance (10)
    CREATE TABLE IF NOT EXISTS nba_criterion_214 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.2.1 Teaching Learning Process (15)
    CREATE TABLE IF NOT EXISTS nba_criterion_221 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.2.2 Quality of Exams/Assignments (15)
    CREATE TABLE IF NOT EXISTS nba_criterion_222 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.2.3 Student Projects (20)
    CREATE TABLE IF NOT EXISTS nba_criterion_223 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.2.4 Industry Interaction (10)
    CREATE TABLE IF NOT EXISTS nba_criterion_224 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- 2.2.5 Internships/Training (10)
    CREATE TABLE IF NOT EXISTS nba_criterion_225 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        marks FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $pdo->exec($sql);
    echo "Tables for Criterion 2 created successfully.";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
