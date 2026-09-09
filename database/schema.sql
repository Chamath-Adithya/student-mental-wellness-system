-- Student Mental Wellness Check-in System
-- Database: wellness_system_db
-- System Architect: B.A.I.D Bopitiya (DIT 14253 - DIT 14 Intake)

CREATE DATABASE IF NOT EXISTS `wellness_system_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wellness_system_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `counseling_requests`;
DROP TABLE IF EXISTS `assessments`;
DROP TABLE IF EXISTS `mood_logs`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table (Students & Counselors/Admins)
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(120) NOT NULL,
    `student_id` VARCHAR(50) NULL, -- e.g. DIT 14253
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('student', 'admin', 'counselor') DEFAULT 'student',
    `intake` VARCHAR(50) DEFAULT 'DIT 14 Intake',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Daily Mood Logs Table (Using Vector Icons, No Emojis)
CREATE TABLE `mood_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `mood_code` VARCHAR(30) NOT NULL, -- thriving, balanced, fatigued, distressed
    `mood_icon` VARCHAR(50) NOT NULL, -- fa-sun, fa-seedling, fa-cloud-rain, fa-bolt
    `mood_label` VARCHAR(80) NOT NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mood_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Mental Health Assessments Table (PHQ-9 & GAD-7)
CREATE TABLE `assessments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `student_name` VARCHAR(120) NOT NULL,
    `test_type` ENUM('phq9', 'gad7') NOT NULL,
    `total_score` INT NOT NULL,
    `severity_level` VARCHAR(80) NOT NULL, -- Minimal, Mild, Moderate, Severe
    `is_high_risk` TINYINT(1) DEFAULT 0,   -- Flagged for immediate counselor review
    `answers_json` TEXT NULL,
    `recommendation` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_assess_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Counseling Session Requests Table
CREATE TABLE `counseling_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `student_name` VARCHAR(120) NOT NULL,
    `student_id` VARCHAR(50) NULL,
    `request_type` ENUM('named', 'anonymous') DEFAULT 'named',
    `preferred_mode` ENUM('online', 'in-person') DEFAULT 'online',
    `preferred_date` DATETIME NOT NULL,
    `notes` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `counselor_feedback` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_counsel_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- SEED DATA
-- ---------------------------------------------------------

-- Password for both accounts: student123 / admin123
INSERT INTO `users` (`id`, `full_name`, `student_id`, `email`, `password_hash`, `role`, `intake`) VALUES
(1, 'Lead Counselor', 'ADM-001', 'admin@sansun.com', '$2y$10$tZ2xTf/7Yq25m52WjA566uK69sB9kR0P8G5J8sI9aY6lq1VnK6O5y', 'admin', 'Faculty of Computing & Health'),
(2, 'B.A.I.D Bopitiya', 'DIT 14253', 'student@dit.ac.lk', '$2y$10$tZ2xTf/7Yq25m52WjA566uK69sB9kR0P8G5J8sI9aY6lq1VnK6O5y', 'student', 'DIT 14 Intake'),
(3, 'Kasun Perera', 'DIT 14210', 'kasun.p@dit.ac.lk', '$2y$10$tZ2xTf/7Yq25m52WjA566uK69sB9kR0P8G5J8sI9aY6lq1VnK6O5y', 'student', 'DIT 14 Intake');

-- Sample Mood Logs (Icon-based)
INSERT INTO `mood_logs` (`user_id`, `mood_code`, `mood_icon`, `mood_label`, `note`, `created_at`) VALUES
(2, 'thriving', 'fa-sun', 'Thriving / ප්‍රබෝධමත්', 'Completed the semester web assignment on schedule.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'balanced', 'fa-seedling', 'Balanced / සන්සුන්', 'Mind is peaceful after morning breathing exercises.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'thriving', 'fa-sun', 'Thriving / ප්‍රබෝධමත්', 'Ready and motivated for upcoming project labs.', NOW()),
(3, 'distressed', 'fa-bolt', 'Distressed / පීඩිතයි', 'Feeling exam anxiety before finals.', NOW());

-- Sample Assessments
INSERT INTO `assessments` (`user_id`, `student_name`, `test_type`, `total_score`, `severity_level`, `is_high_risk`, `recommendation`, `created_at`) VALUES
(2, 'B.A.I.D Bopitiya', 'phq9', 3, 'Minimal / සාමාන්‍ය', 0, 'Mental wellness indicators are within healthy ranges. Continue regular study breaks and sleep routines.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'B.A.I.D Bopitiya', 'gad7', 5, 'Mild Anxiety / සුළු කාංසාව', 0, 'Slight anxiety detected. Practice 4-7-8 breathing and sensory grounding techniques.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'Kasun Perera', 'phq9', 17, 'Moderately Severe / වැඩි විෂාදය', 1, 'Significant distress indicated. Prompt connection with institutional counseling and 1926 hotline recommended.', NOW());

-- Sample Counseling Requests
INSERT INTO `counseling_requests` (`user_id`, `student_name`, `student_id`, `request_type`, `preferred_mode`, `preferred_date`, `notes`, `status`, `created_at`) VALUES
(2, 'B.A.I.D Bopitiya', 'DIT 14253', 'named', 'online', DATE_ADD(NOW(), INTERVAL 2 DAY), 'Seeking guidance on academic time optimization and stress reduction.', 'approved', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'Kasun Perera', 'DIT 14210', 'named', 'in-person', DATE_ADD(NOW(), INTERVAL 3 DAY), 'Discussion regarding exam preparation anxiety and concentration.', 'pending', NOW()),
(2, 'Anonymous Student', 'Anonymous', 'anonymous', 'online', DATE_ADD(NOW(), INTERVAL 4 DAY), 'Confidential discussion regarding personal balance.', 'pending', NOW());
