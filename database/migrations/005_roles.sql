-- Update existing roles table for RBAC
-- This migration adds the patient role and ensures proper structure

-- First, check if table exists and modify
ALTER TABLE `roles` 
MODIFY COLUMN `description` TEXT;

-- Insert Patient role if not exists
INSERT IGNORE INTO `roles` (`name`, `slug`, `description`) VALUES
('Patient', 'patient', 'Patient with limited access to own appointments and profile');

-- Update existing roles descriptions
UPDATE `roles` SET `description` = 'Full system access with all permissions including role and permission management' WHERE `slug` = 'super_admin';
UPDATE `roles` SET `description` = 'Administrative access with user, patient, appointment, and queue management' WHERE `slug` = 'admin';
UPDATE `roles` SET `description` = 'Staff access for appointment and queue management' WHERE `slug` = 'staff';
UPDATE `roles` SET `description` = 'Doctor access for patient management and appointments' WHERE `slug` = 'doctor';
UPDATE `roles` SET `description` = 'Receptionist access for appointment scheduling and queue management' WHERE `slug` = 'receptionist';
