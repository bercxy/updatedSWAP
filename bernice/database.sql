-- ========================================
-- DATABASE SETUP FILE
-- ========================================
-- Run this in phpMyAdmin: http://localhost/phpmyadmin
-- Click "SQL" tab, paste this code, click "Go"

-- ========================================
-- CREATE DATABASE
-- ========================================

CREATE DATABASE IF NOT EXISTS bookings_db;

USE bookings_db;

-- ========================================
-- CREATE BOOKINGS TABLE
-- ========================================

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    facility_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- INSERT SAMPLE DATA
-- ========================================

INSERT INTO bookings
(user_id, facility_id, booking_date, start_time, end_time, status)
VALUES
(1, 101, '2026-02-16', '09:00:00', '11:00:00', 'pending'),
(2, 101, '2026-02-16', '11:00:00', '13:00:00', 'pending'),
(3, 102, '2026-02-17', '10:00:00', '12:00:00', 'pending');

-- ========================================
-- VERIFICATION (Optional)
-- ========================================
-- SELECT * FROM bookings;
-- DESCRIBE bookings;

-- Setup complete! Your database is ready to use.
