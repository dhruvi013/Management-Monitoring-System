-- NBA Tables for Criteria 4.1 and 4.2
-- Run this after database.sql to add NBA monitoring tables

USE department_monitoring;

-- Drop existing table and recreate with proper structure
DROP TABLE IF EXISTS nba_enrollment_41;

-- 4.1 Enrollment Ratio (20 marks)
CREATE TABLE nba_enrollment_41 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  intake INT NOT NULL,
  admitted INT NOT NULL,
  enrollment_ratio DECIMAL(5,2) NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4.2.1 Success rate without backlog (15 marks)
CREATE TABLE nba_success_421 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  admitted_degree INT NOT NULL,
  admitted_d2d INT NOT NULL,
  total_admitted INT NOT NULL,
  graduated_wo_back INT NOT NULL,
  success_index DECIMAL(5,4) NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4.2.2 Success rate in stipulated period (5 marks)
CREATE TABLE nba_success_422 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  admitted_degree INT NOT NULL,
  admitted_d2d INT NOT NULL,
  total_admitted INT NOT NULL,
  graduated_w_back INT NOT NULL,
  success_index DECIMAL(5,4) NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
