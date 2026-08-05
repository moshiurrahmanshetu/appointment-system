-- Ensure proper foreign key constraints and user role relationships

-- The users table already has role_id, so we just need to ensure it's properly set
-- This migration ensures the admin user has the correct role

-- Update the default admin to have Super Admin role (id should be 1)
UPDATE `users` SET `role_id` = 1 WHERE `user_id` = 'admin';

-- Add foreign key constraint if it doesn't exist
ALTER TABLE `users` 
ADD CONSTRAINT `fk_users_role` 
FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Add foreign key constraint for role_permissions if it doesn't exist
ALTER TABLE `role_permissions` 
ADD CONSTRAINT `fk_role_permissions_role` 
FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `role_permissions` 
ADD CONSTRAINT `fk_role_permissions_permission` 
FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
