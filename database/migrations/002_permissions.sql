-- Permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role-Permission pivot table
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default permissions
INSERT INTO `permissions` (`name`, `slug`, `description`) VALUES
('View Dashboard', 'view_dashboard', 'Access to dashboard'),
('Manage Users', 'manage_users', 'Create, edit, and delete users'),
('Manage Roles', 'manage_roles', 'Manage system roles and permissions'),
('Manage Patients', 'manage_patients', 'Create, edit, and delete patients'),
('Manage Appointments', 'manage_appointments', 'Create, edit, and delete appointments'),
('Manage Queue', 'manage_queue', 'Manage appointment queue'),
('View Reports', 'view_reports', 'Access to system reports'),
('Manage Settings', 'manage_settings', 'Access to system settings');
