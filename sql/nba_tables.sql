-- NBA Tables for All Criteria

USE department_monitoring;

-- Drop existing tables and recreate with proper structure
DROP TABLE IF EXISTS nba_enrollment_41;
DROP TABLE IF EXISTS nba_success_421;
DROP TABLE IF EXISTS nba_success_422;
DROP TABLE IF EXISTS nba_academic_43;
DROP TABLE IF EXISTS nba_placement_44;
DROP TABLE IF EXISTS nba_professional_451;
DROP TABLE IF EXISTS nba_publications_452;
DROP TABLE IF EXISTS nba_participation_453;

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

-- 4.5.1 Professional Chapters and Events (5 marks)
CREATE TABLE nba_professional_451 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  no_of_chapters INT NOT NULL,
  international_events INT NOT NULL,
  national_events INT NOT NULL,
  state_events INT NOT NULL,
  dept_events INT NOT NULL,
  marks_a DECIMAL(5,2) NOT NULL,
  marks_b DECIMAL(5,2) NOT NULL,
  total_marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4.5.2 Publications (Magazine/Newsletter) (5 marks)
CREATE TABLE nba_publications_452 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  magazine ENUM('Yes', 'No') NOT NULL,
  target_freq1 INT NOT NULL,
  newsletter ENUM('Yes', 'No') NOT NULL,
  target_freq2 INT NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE nba_publications_452
ADD COLUMN num_magazine INT DEFAULT 0,
ADD COLUMN num_newsletter INT DEFAULT 0;


-- 4.5.3 Student Participation in Events (10 marks)
CREATE TABLE nba_participation_453 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(20) NOT NULL,
  total_participation INT NOT NULL,
  participation_within_state INT NOT NULL,
  participation_outside_state INT NOT NULL,
  awards INT NOT NULL,
  within_state_percentage DECIMAL(5,2) NOT NULL,
  outside_state_percentage DECIMAL(5,2) NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5.1 Student-Faculty Ratio (SFR)
CREATE TABLE nba_criterion_51 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  num_students INT NOT NULL,
  num_faculty INT NOT NULL,
  sfr FLOAT NOT NULL,
  avg_sfr FLOAT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5.2 Faculty Cadre Proportion
CREATE TABLE nba_criterion_52 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  req_prof INT NOT NULL,
  avail_prof INT NOT NULL,
  req_assoc INT NOT NULL,
  avail_assoc INT NOT NULL,
  req_asst INT NOT NULL,
  avail_asst INT NOT NULL,
  avg_rf1 FLOAT DEFAULT 0,
  avg_af1 FLOAT DEFAULT 0,
  ratio1 FLOAT DEFAULT 0,
  avg_rf2 FLOAT DEFAULT 0,
  avg_af2 FLOAT DEFAULT 0,
  ratio2 FLOAT DEFAULT 0,
  avg_rf3 FLOAT DEFAULT 0,
  avg_af3 FLOAT DEFAULT 0,
  ratio3 FLOAT DEFAULT 0,
  marks FLOAT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5.3 Faculty Qualification
CREATE TABLE nba_criterion_53 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  x_phd INT NOT NULL,
  y_mtech INT NOT NULL,
  f_required INT NOT NULL,
  fq_score FLOAT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6.1 Adequate and well equipped laboratories, and technical manpower (40)
CREATE TABLE nba_criterion_61 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  details TEXT NOT NULL,
  marks FLOAT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6.2 Laboratories: Maintenance and overall ambience (10)
CREATE TABLE nba_criterion_62 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  details TEXT NOT NULL,
  marks FLOAT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6.3 Safety measures in laboratories (10)
CREATE TABLE nba_criterion_63 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  details TEXT NOT NULL,
  marks FLOAT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6.4 Project laboratory/Facultities (20)
CREATE TABLE nba_criterion_64 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  academic_year VARCHAR(50) NOT NULL,
  details TEXT NOT NULL,
  marks FLOAT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
