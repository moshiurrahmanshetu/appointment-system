-- Add appointment management permissions
INSERT IGNORE INTO `permissions` (`name`, `slug`, `description`, `created_at`) VALUES
('View Appointments', 'appointments.view', 'Permission to view appointment records', NOW()),
('Create Appointments', 'appointments.create', 'Permission to create new appointments', NOW()),
('Edit Appointments', 'appointments.edit', 'Permission to edit appointment information', NOW()),
('Delete Appointments', 'appointments.delete', 'Permission to soft delete appointment records', NOW()),
('Restore Appointments', 'appointments.restore', 'Permission to restore deleted appointment records', NOW());

-- Assign appointment permissions to admin role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'admin'
AND p.slug IN ('appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete', 'appointments.restore');

-- Assign appointment permissions to doctor role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'doctor'
AND p.slug IN ('appointments.view', 'appointments.create', 'appointments.edit');

-- Assign appointment permissions to receptionist role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'receptionist'
AND p.slug IN ('appointments.view', 'appointments.create', 'appointments.edit');