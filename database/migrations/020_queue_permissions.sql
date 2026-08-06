-- Add queue management permissions
INSERT IGNORE INTO `permissions` (`name`, `slug`, `description`, `created_at`) VALUES
('View Queue', 'queue.view', 'Permission to view queue records', NOW()),
('Manage Queue', 'queue.manage', 'Permission to manage queue operations', NOW()),
('Call Patient', 'queue.call', 'Permission to call patients from queue', NOW()),
('Complete Queue', 'queue.complete', 'Permission to complete queue entries', NOW()),
('Skip Queue', 'queue.skip', 'Permission to skip queue entries', NOW()),
('Cancel Queue', 'queue.cancel', 'Permission to cancel queue entries', NOW());

-- Assign queue permissions to admin role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'admin'
AND p.slug IN ('queue.view', 'queue.manage', 'queue.call', 'queue.complete', 'queue.skip', 'queue.cancel');

-- Assign queue permissions to doctor role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'doctor'
AND p.slug IN ('queue.view', 'queue.manage', 'queue.call', 'queue.complete', 'queue.skip');

-- Assign queue permissions to receptionist role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'receptionist'
AND p.slug IN ('queue.view', 'queue.manage', 'queue.call', 'queue.skip');