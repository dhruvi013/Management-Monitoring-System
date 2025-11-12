-- Create database (change name if you prefer)
CREATE DATABASE IF NOT EXISTS department_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE department_monitoring;

-- Students table
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  last_name VARCHAR(100),
  gr_no VARCHAR(50) NOT NULL UNIQUE,
  enrollment_no VARCHAR(100) NOT NULL UNIQUE,
  class VARCHAR(100) NOT NULL,
  batch VARCHAR(50),
  academic_year VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
