-- Settings table
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    `type` VARCHAR(20) DEFAULT 'string',
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`, `description`) VALUES
('app_name', 'Appointment Queue System', 'string', 'Application name'),
('app_version', '1.0.0', 'string', 'Application version'),
('timezone', 'UTC', 'string', 'Default timezone'),
('date_format', 'Y-m-d', 'string', 'Default date format'),
('time_format', 'H:i', 'string', 'Default time format'),
('items_per_page', '25', 'integer', 'Number of items per page'),
('session_lifetime', '7200', 'integer', 'Session lifetime in seconds'),
('maintenance_mode', '0', 'boolean', 'Maintenance mode status');
