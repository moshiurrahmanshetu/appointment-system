-- Roles table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles
INSERT INTO `roles` (`name`, `slug`, `description`) VALUES
('Super Admin', 'super_admin', 'Full system access with all permissions'),
('Admin', 'admin', 'Administrative access with most permissions'),
('Staff', 'staff', 'Staff access with limited permissions'),
('Doctor', 'doctor', 'Doctor access for patient management'),
('Receptionist', 'receptionist', 'Receptionist access for appointment management');
