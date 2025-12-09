-- Create database (change name if you prefer)
CREATE DATABASE IF NOT EXISTS department_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE department_monitoring;

CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,

  -- Core fields coming from Excel
  gr_no VARCHAR(50) NOT NULL UNIQUE,
  enrollment_no VARCHAR(100) NOT NULL UNIQUE,
  class VARCHAR(100) NOT NULL,
  semester INT NOT NULL,

  -- Name fields created automatically
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  last_name VARCHAR(100),

  -- This will NOT come from excel, user manually selects
  batch VARCHAR(50) NOT NULL,

  academic_year VARCHAR(20) NOT NULL DEFAULT '2025',

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- ALTER TABLE students
-- MODIFY academic_year VARCHAR(20) NOT NULL;



-- UPDATE students
-- SET academic_year = '2024-25'
-- WHERE academic_year = '2025' OR academic_year NOT LIKE '%-%';



CREATE TABLE faculty (
  id INT AUTO_INCREMENT PRIMARY KEY,

  faculty_id VARCHAR(100) NOT NULL UNIQUE,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  last_name VARCHAR(100) NOT NULL,

  department VARCHAR(100) NOT NULL,
  designation VARCHAR(100) NOT NULL,

  academic_year VARCHAR(20) NOT NULL DEFAULT '2025',

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



