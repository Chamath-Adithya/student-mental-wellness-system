-- Student Mental Wellness Check-in System (Sansun - සන්සුන්)
-- Database: wellness_system_db
-- Project by: B.A.I.D Bopitiya (DIT 14253 - DIT 14 Intake)

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

-- 2. Daily Mood Logs Table
CREATE TABLE `mood_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `mood_emoji` VARCHAR(10) NOT NULL,
    `mood_label` VARCHAR(50) NOT NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mood_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Mental Health Assessments Table (PHQ-9 & GAD-7)
CREATE TABLE `assessments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `student_name` VARCHAR(120) NULL,
    `test_type` ENUM('phq9', 'gad7') NOT NULL,
    `total_score` INT NOT NULL,
    `severity_level` VARCHAR(50) NOT NULL, -- Minimal, Mild, Moderate, Severe
    `is_high_risk` TINYINT(1) DEFAULT 0,  -- Flagged for immediate counselor review
    `answers_json` TEXT NULL,
    `recommendation` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_assess_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Counseling Session Requests Table
CREATE TABLE `counseling_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `student_name` VARCHAR(120) NOT NULL,
    `student_id` VARCHAR(50) NULL,
    `request_type` ENUM('named', 'anonymous') DEFAULT 'named',
    `preferred_mode` ENUM('online', 'in-person') DEFAULT 'online',
    `preferred_date` DATETIME NOT NULL,
    `notes` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `counselor_feedback` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_counsel_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- SEED DATA
-- ---------------------------------------------------------

-- Password for both accounts is: admin123 / student123 (hashed via BCRYPT)
-- admin: admin123
-- student: student123
INSERT INTO `users` (`id`, `full_name`, `student_id`, `email`, `password_hash`, `role`, `intake`) VALUES
(1, 'Admin Counselor', 'ADM-001', 'admin@sansun.com', '$2y$10$tZ2xTf/7Yq25m52WjA566uK69sB9kR0P8G5J8sI9aY6lq1VnK6O5y', 'admin', 'Faculty of Computing'),
(2, 'B.A.I.D Bopitiya', 'DIT 14253', 'student@dit.ac.lk', '$2y$10$tZ2xTf/7Yq25m52WjA566uK69sB9kR0P8G5J8sI9aY6lq1VnK6O5y', 'student', 'DIT 14 Intake'),
(3, 'Kasun Perera', 'DIT 14210', 'kasun.p@dit.ac.lk', '$2y$10$tZ2xTf/7Yq25m52WjA566uK69sB9kR0P8G5J8sI9aY6lq1VnK6O5y', 'student', 'DIT 14 Intake');

-- Sample Mood Logs
INSERT INTO `mood_logs` (`user_id`, `mood_emoji`, `mood_label`, `note`, `created_at`) VALUES
(2, '😊', 'Happy', 'Completed the web programming assignment successfully.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, '😐', 'Neutral', 'A bit tired after long lab lectures.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, '😊', 'Happy', 'Feeling relaxed and ready for the week.', NOW()),
(3, '😔', 'Sad', 'Exam pressure is getting overwhelming.', NOW());

-- Sample Assessments
INSERT INTO `assessments` (`user_id`, `student_name`, `test_type`, `total_score`, `severity_level`, `is_high_risk`, `recommendation`, `created_at`) VALUES
(2, 'B.A.I.D Bopitiya', 'phq9', 4, 'Minimal / සාමාන්‍ය', 0, 'Your score indicates minimal depressive symptoms. Continue your healthy daily self-care habits.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'B.A.I.D Bopitiya', 'gad7', 6, 'Mild Anxiety / සුළු කාංසාව', 0, 'Mild anxiety identified. Try the 4-7-8 breathing and grounding exercises.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'Kasun Perera', 'phq9', 18, 'Moderately Severe / දැඩි විෂාදය', 1, 'High stress levels detected. Immediate connection with university counseling or calling 1926 is strongly recommended.', NOW());

-- Sample Counseling Requests
INSERT INTO `counseling_requests` (`user_id`, `student_name`, `student_id`, `request_type`, `preferred_mode`, `preferred_date`, `notes`, `status`, `created_at`) VALUES
(2, 'B.A.I.D Bopitiya', 'DIT 14253', 'named', 'online', DATE_ADD(NOW(), INTERVAL 2 DAY), 'Discussion regarding time management and exam stress mitigation.', 'approved', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'Kasun Perera', 'DIT 14210', 'named', 'in-person', DATE_ADD(NOW(), INTERVAL 3 DAY), 'Feeling overwhelmed by coursework deadlines. Would like to talk to a counselor.', 'pending', NOW()),
(NULL, 'Anonymous Student', 'Anonymous', 'anonymous', 'online', DATE_ADD(NOW(), INTERVAL 4 DAY), 'Seeking confidential guidance on personal stress.', 'pending', NOW());
