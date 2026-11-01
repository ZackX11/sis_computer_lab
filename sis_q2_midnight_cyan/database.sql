CREATE DATABASE IF NOT EXISTS student_information_system
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_information_system;

-- Minimal master tables for running Q2 independently.
-- If you already created these tables for Q1, keep your existing tables
-- and ensure the ID columns and profile columns used by the PHP files exist.
CREATE TABLE IF NOT EXISTS student (
    Student_ID VARCHAR(30) PRIMARY KEY,
    First_Name VARCHAR(60) NOT NULL,
    Last_Name VARCHAR(60) DEFAULT NULL,
    Email VARCHAR(120) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS faculty (
    Faculty_ID VARCHAR(30) PRIMARY KEY,
    First_Name VARCHAR(60) NOT NULL,
    Last_Name VARCHAR(60) DEFAULT NULL,
    Email VARCHAR(120) DEFAULT NULL,
    Designation VARCHAR(80) DEFAULT NULL
) ENGINE=InnoDB;

-- Stakeholder_Type is added because User_ID may refer to either Student or Faculty.
CREATE TABLE IF NOT EXISTS login (
    Login_ID INT AUTO_INCREMENT PRIMARY KEY,
    User_ID VARCHAR(30) NOT NULL UNIQUE,
    Stakeholder_Type ENUM('Student','Faculty') NOT NULL,
    Username VARCHAR(60) NOT NULL,
    Password_Hash CHAR(64) NOT NULL,
    Random_salt CHAR(32) NOT NULL,
    Last_Login DATETIME DEFAULT NULL,
    CONSTRAINT chk_login_type CHECK (Stakeholder_Type IN ('Student','Faculty'))
) ENGINE=InnoDB;

-- Demo records. User IDs satisfy the lab's 10-character ID rule.
INSERT IGNORE INTO student (Student_ID, First_Name, Last_Name, Email)
VALUES ('StuDemo2026', 'Demo', 'Student', 'student@example.com');

INSERT IGNORE INTO faculty (Faculty_ID, First_Name, Last_Name, Email, Designation)
VALUES ('FacDemo2026', 'Demo', 'Faculty', 'faculty@example.com', 'Assistant Professor');
