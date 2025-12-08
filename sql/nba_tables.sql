-- NBA Tables for Criteria 4.1, 4.2, 4.3, and 4.4

USE department_monitoring;

-- Drop existing tables and recreate with proper structure
DROP TABLE IF EXISTS nba_enrollment_41;
DROP TABLE IF EXISTS nba_success_421;
DROP TABLE IF EXISTS nba_success_422;
DROP TABLE IF EXISTS nba_academic_43;

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

-- 4.3 Academic Performance in Second Year (10 marks)
CREATE TABLE nba_academic_43 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  admitted_degree INT NOT NULL,
  admitted_d2d INT NOT NULL,
  total_admitted INT NOT NULL,
  sem3_avg_sgpa DECIMAL(5,2) NOT NULL,
  sem4_avg_sgpa DECIMAL(5,2) NOT NULL,
  sem3_credit INT NOT NULL,
  sem4_credit INT NOT NULL,
  total_mean_cgpa DECIMAL(5,2) NOT NULL,
  success_2ndyear INT NOT NULL,
  students_appeared INT NOT NULL,
  success_rate DECIMAL(5,2) NOT NULL,
  api DECIMAL(5,4) NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4.4 Placement and Career Progression (30 marks)
CREATE TABLE nba_placement_44 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  final_year_total INT NOT NULL,
  placed INT NOT NULL,
  higher_studies INT NOT NULL,
  entrepreneur INT NOT NULL,
  assessment_index DECIMAL(5,4) NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
