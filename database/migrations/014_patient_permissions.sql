-- Add patient management permissions (ignore duplicates)
INSERT IGNORE INTO `permissions` (`name`, `slug`, `description`, `created_at`) VALUES
('View Patients', 'patients.view', 'Permission to view patient records and profiles', NOW()),
('Create Patients', 'patients.create', 'Permission to create new patient records', NOW()),
('Edit Patients', 'patients.edit', 'Permission to edit patient information', NOW()),
('Delete Patients', 'patients.delete', 'Permission to soft delete patient records', NOW()),
('Restore Patients', 'patients.restore', 'Permission to restore deleted patient records', NOW()),
('Change Patient Status', 'patients.status', 'Permission to change patient status (active/inactive/blocked)', NOW());

-- Assign patient permissions to admin role (ignore duplicates)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'admin'
AND p.slug IN ('patients.view', 'patients.create', 'patients.edit', 'patients.delete', 'patients.restore', 'patients.status');

-- Assign patient permissions to doctor role (ignore duplicates)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'doctor'
AND p.slug IN ('patients.view', 'patients.create', 'patients.edit');

-- Assign patient permissions to nurse role (ignore duplicates)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'nurse'
AND p.slug IN ('patients.view', 'patients.create', 'patients.edit');

-- Assign patient permissions to receptionist role (ignore duplicates)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'receptionist'
AND p.slug IN ('patients.view', 'patients.create', 'patients.edit');
