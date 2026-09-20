-- ============================================================
-- Single Course Store - Database Schema
-- Version: 1.0
-- Engine: MySQL 8.0 / InnoDB, utf8mb4
-- To import:  mysql -h 127.0.0.1 -P 3307 -u root -p < sql/schema.sql
-- ============================================================

DROP DATABASE IF EXISTS course_store_db;
CREATE DATABASE course_store_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE course_store_db;

-- ------------------------------------------------------------
-- Table: admins (single admin / manager for now)
-- ------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL DEFAULT 'مدير النظام',
    role ENUM('admin', 'manager') NOT NULL DEFAULT 'admin',
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: courses (single product currently, ready for many)
-- ------------------------------------------------------------
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: orders
-- ------------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_txn_id VARCHAR(100) DEFAULT NULL,
    status ENUM('pending', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_order_number (order_number),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: order_items (supports multiple courses in future)
-- ------------------------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    course_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Seed data
-- ------------------------------------------------------------

-- NOTE: admin account is created by setup.php (generates a proper bcrypt hash).
-- Default credentials after setup: username = admin / password = admin123

-- Default course
INSERT INTO courses (name, description, price, image) VALUES
('دورة تطوير تطبيقات PHP', 'دورة تدريبية برمجية شاملة تعلّمك بناء تطبيقات ويب احترافية باستخدام PHP و MySQL و Bootstrap، من الصفر حتى الاحتراف. تشمل المشاريع العملية والدعم المباشر.', 199.00, 'assets/images/course.svg');

-- Sample order (so dashboard / orders pages have data to show)
INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, customer_address, notes, total_amount, payment_txn_id, status) VALUES
('ORD20240001', 'أحمد محمد', 'ahmed@example.com', '0501234567', 'الرياض - حي النرجس', 'يرغب بتسليم الشهادة بالبريد', 199.00, 'TXN-TEST-0001', 'paid');

INSERT INTO order_items (order_id, course_id, quantity, price) VALUES
(1, 1, 1, 199.00);
