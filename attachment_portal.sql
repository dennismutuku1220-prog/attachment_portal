-- Attachment Portal SQL schema
CREATE DATABASE IF NOT EXISTS attachment_portal;
USE attachment_portal;

-- Users table stores all account types
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'supervisor', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student profile information
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    registration_number VARCHAR(50) NOT NULL,
    course VARCHAR(100) NOT NULL,
    institution VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Supervisor profile information
CREATE TABLE IF NOT EXISTS supervisors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Student attachments (company details)
CREATE TABLE IF NOT EXISTS attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Weekly reports submitted by students
CREATE TABLE IF NOT EXISTS weekly_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    week_number INT NOT NULL,
    tasks_done TEXT NOT NULL,
    challenges TEXT NOT NULL,
    supervisor_remarks TEXT,
    score INT,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Uploaded documents
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    upload_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Supervisor assignments to students
CREATE TABLE IF NOT EXISTS supervisor_assignments (
    student_id INT PRIMARY KEY,
    supervisor_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES supervisors(id) ON DELETE CASCADE
);

-- Default users for testing
INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@portal.test', '$2y$10$h/fPxDw1kWU7CdKi0Q0imeY.JSkmQy9moWF8edm9Ay8K30gohIxA.', 'admin'),
('Jane Student', 'student@portal.test', '$2y$10$xD9mEzXCUA2XkAImBclyLOHfTI/lGrLcVaG3A99XRXbLjrbJlCMyC', 'student'),
('John Supervisor', 'supervisor@portal.test', '$2y$10$W8LoQURZcBbXMQESXFOMIulArgltg5/hDZGXbaG8gW2Nhdh1SrOya', 'supervisor');

INSERT INTO students (user_id, registration_number, course, institution) VALUES
((SELECT id FROM users WHERE email = 'student@portal.test'), 'REG123', 'BSc Computer Science', 'Tech University');

INSERT INTO supervisors (user_id, department) VALUES
((SELECT id FROM users WHERE email = 'supervisor@portal.test'), 'ICT Department');

INSERT INTO supervisor_assignments (student_id, supervisor_id)
VALUES (
    (SELECT id FROM students WHERE registration_number = 'REG123'),
    (SELECT id FROM supervisors LIMIT 1)
);

-- Additional supervisors for production use
-- NOTE: Before running this, generate the password hashes by opening
-- http://localhost/attachment_portal/hash_passwords.php
-- and copy the hashes for Bosco123, Michael123, and Jeremiah123.

INSERT INTO users (name, email, password, role) VALUES
('Bosco Mulwa',   'bosco.mulwa@makueni.go.ke',    '$2y$10$FSUtxIlOw/tJ0Yr8ATzHxuZ2IhHrJWeMTtE1NCHzT2sHxlcqzYaja', 'supervisor'),
('Michael Mutie', 'michael.mutie@makueni.go.ke',  '$2y$10$YY0nYxVjK2Hq15/agWhz0OJ6Ck4F2V3.RCe2Tis35comCecvxS5yq',  'supervisor'),
('Jeremiah Muuo', 'jeremiah.muuo@makueni.go.ke',  '$2y$10$taCCpp30tmNIZaAb5RVGUOB34PDUB25/mRQnJZpk4fxfJSd.Woydm', 'supervisor');

INSERT INTO supervisors (user_id, department) VALUES
((SELECT id FROM users WHERE email = 'bosco.mulwa@makueni.go.ke'),    'ICT Department'),
((SELECT id FROM users WHERE email = 'michael.mutie@makueni.go.ke'),  'ICT Department'),
((SELECT id FROM users WHERE email = 'jeremiah.muuo@makueni.go.ke'),  'ICT Department');

