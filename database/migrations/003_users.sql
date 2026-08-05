-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100),
    `phone` VARCHAR(20),
    `address` TEXT,
    `avatar` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT 1,
    `remember_token` VARCHAR(100),
    `last_login_at` TIMESTAMP NULL,
    `last_login_ip` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_role_id` (`role_id`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default Super Admin user
-- Password: admin123 (hashed using password_hash() with default algorithm)
-- Hash generated using: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`user_id`, `password`, `role_id`, `full_name`, `email`, `is_active`) VALUES
('admin', '$2y$10$.UvesDct4TMdqSkWKprbHuZTBYDDrfhBNywTW6uCv3x74hfG5X5Wi', 1, 'Super Admin', 'admin@example.com', 1);
