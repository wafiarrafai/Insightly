
-- ==========================================
-- INSIGHTLY
-- Customer Feedback Management System
-- ==========================================

CREATE DATABASE IF NOT EXISTS insightly_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE insightly_db;


-- ==========================================
-- TABLE: CUSTOMERS
-- ==========================================

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ==========================================
-- TABLE: FEEDBACK
-- ==========================================

CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    rating TINYINT NOT NULL,
    category VARCHAR(50) NOT NULL,
    comment TEXT NOT NULL,
    feedback_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_feedback_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT chk_feedback_rating
        CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;


-- ==========================================
-- INDEXES
-- ==========================================

CREATE INDEX idx_customer_name
ON customers(customer_name);

CREATE INDEX idx_feedback_customer
ON feedback(customer_id);

CREATE INDEX idx_feedback_category
ON feedback(category);

CREATE INDEX idx_feedback_date
ON feedback(feedback_date);


-- ==========================================
-- DATABASE COMPLETED
-- ==========================================
