-- Add consultation management permissions
INSERT IGNORE INTO `permissions` (`name`, `slug`, `description`, `created_at`) VALUES
('View Consultations', 'consultation.view', 'Permission to view consultation records', NOW()),
('Create Consultations', 'consultation.create', 'Permission to create new consultations', NOW()),
('Edit Consultations', 'consultation.edit', 'Permission to edit consultation information', NOW()),
('Delete Consultations', 'consultation.delete', 'Permission to soft delete consultation records', NOW()),
('Complete Consultations', 'consultation.complete', 'Permission to complete consultations', NOW());

-- Assign consultation permissions to admin role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'admin'
AND p.slug IN ('consultation.view', 'consultation.create', 'consultation.edit', 'consultation.delete', 'consultation.complete');

-- Assign consultation permissions to doctor role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'doctor'
AND p.slug IN ('consultation.view', 'consultation.create', 'consultation.edit', 'consultation.complete');