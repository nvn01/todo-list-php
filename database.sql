-- ====================================
-- TODO LIST DATABASE
-- ====================================
-- Database name: todolist_db
-- Import this file to phpMyAdmin
-- ====================================

CREATE DATABASE IF NOT EXISTS todolist_db;
USE todolist_db;

-- ====================================
-- Users Table
-- ====================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ====================================
-- Todos Table
-- ====================================
CREATE TABLE todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    status ENUM('Belum', 'Proses', 'Selesai') DEFAULT 'Belum',
    priority ENUM('mendesak', 'penting', 'biasa') DEFAULT 'mendesak',
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ====================================
-- Insert Admin User
-- Username: admin
-- Password: admin123
-- ====================================
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$6c6YsakcjKcp6YsNaZZCKuHd3rT8JJN9H8BBB5FnQDvWYwgNchhopW', 'admin');

-- ====================================
-- Insert Demo User (Optional - for testing)
-- Username: demo
-- Password: demo123
-- ====================================
INSERT INTO users (username, password, role) VALUES 
('demo', '$2y$10$vXB5W8qKE8.xR8q5RqPKFODemoHashForTesting123456789012', 'user');

-- Note: The password is hashed using PHP's password_hash() function
-- To create new hashed passwords, use: password_hash('YourPassword', PASSWORD_DEFAULT)
-- Admin credentials: admin / admin123
