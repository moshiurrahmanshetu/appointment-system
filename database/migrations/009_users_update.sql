-- Update users table for Phase 3 User Management
-- Add new fields required for comprehensive user management

-- Add new columns (MySQL doesn't support IF NOT EXISTS for columns)
-- These will fail if columns already exist, which is expected for re-runs

ALTER TABLE `users` 
ADD COLUMN `username` VARCHAR(50) NULL UNIQUE AFTER `user_id`,
ADD COLUMN `gender` ENUM('male', 'female', 'other') NULL AFTER `email`,
ADD COLUMN `status` ENUM('active', 'inactive', 'blocked') DEFAULT 'active' AFTER `is_active`,
ADD COLUMN `created_by` INT UNSIGNED NULL AFTER `updated_at`,
ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_by`;

-- Add indexes
ALTER TABLE `users` 
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_deleted_at` (`deleted_at`),
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_username` (`username`);

-- Update existing users to set status based on is_active
UPDATE `users` SET `status` = CASE 
    WHEN `is_active` = 1 THEN 'active'
    ELSE 'inactive'
END;

-- Add foreign key for created_by
ALTER TABLE `users` 
ADD CONSTRAINT `fk_users_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
