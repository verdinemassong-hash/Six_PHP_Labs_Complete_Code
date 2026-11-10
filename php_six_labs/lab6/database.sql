CREATE DATABASE IF NOT EXISTS php_practical_labs
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE php_practical_labs;

CREATE TABLE IF NOT EXISTS students (
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(80) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 telephone CHAR(9) NOT NULL,
 date_of_birth DATE NOT NULL,
 programme VARCHAR(100) NOT NULL,
 study_level VARCHAR(50) NOT NULL,
 gender VARCHAR(20) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS loans (
 id INT AUTO_INCREMENT PRIMARY KEY,
 student_id VARCHAR(50) NOT NULL,
 isbn VARCHAR(13) NOT NULL,
 book_title VARCHAR(200) NOT NULL,
 borrow_date DATE NOT NULL,
 due_date DATE NOT NULL,
 loan_duration INT NOT NULL,
 purpose VARCHAR(500) NOT NULL,
 status ENUM('Active','Returned') NOT NULL DEFAULT 'Active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_active_loan(student_id,isbn,status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NULL,
 phone VARCHAR(30) NOT NULL,
 department VARCHAR(100) NOT NULL,
 doctor VARCHAR(100) NOT NULL,
 appointment_date DATE NOT NULL,
 time_slot VARCHAR(30) NOT NULL,
 reason VARCHAR(500) NOT NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unique_doctor_slot(doctor,appointment_date,time_slot)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_name VARCHAR(100) NOT NULL,
 sku VARCHAR(80) NOT NULL UNIQUE,
 category VARCHAR(100) NOT NULL,
 unit_price DECIMAL(12,2) NOT NULL,
 quantity INT NOT NULL,
 description VARCHAR(1000) NULL,
 image_path VARCHAR(255) NULL,
 available TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS event_registrations (
 id INT AUTO_INCREMENT PRIMARY KEY,
 participant_name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL,
 phone VARCHAR(30) NOT NULL,
 workshop VARCHAR(120) NOT NULL,
 attendance_mode VARCHAR(30) NOT NULL,
 seats TINYINT NOT NULL,
 special_requirements VARCHAR(300) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unique_email_workshop(email,workshop)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS support_tickets (
 id INT AUTO_INCREMENT PRIMARY KEY,
 customer_name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL,
 category VARCHAR(100) NOT NULL,
 priority VARCHAR(20) NOT NULL,
 subject VARCHAR(120) NOT NULL,
 description VARCHAR(2000) NOT NULL,
 screenshot_path VARCHAR(255) NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
