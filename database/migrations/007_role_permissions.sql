-- Assign permissions to roles

-- Super Admin - All permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 
    (SELECT id FROM roles WHERE slug = 'super_admin'),
    id
FROM permissions;

-- Admin - Most permissions except role management
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 
    (SELECT id FROM roles WHERE slug = 'admin'),
    id
FROM permissions
WHERE slug NOT IN ('roles.view', 'roles.manage');

-- Receptionist - Appointments, Queue, limited Profile
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 
    (SELECT id FROM roles WHERE slug = 'receptionist'),
    id
FROM permissions
WHERE slug IN (
    'dashboard.view',
    'appointments.view',
    'appointments.create',
    'appointments.edit',
    'queue.view',
    'queue.manage',
    'profile.view',
    'profile.edit',
    'profile.change_password'
);

-- Doctor - Patients, Appointments, Profile
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 
    (SELECT id FROM roles WHERE slug = 'doctor'),
    id
FROM permissions
WHERE slug IN (
    'dashboard.view',
    'patients.view',
    'patients.create',
    'patients.edit',
    'appointments.view',
    'appointments.create',
    'appointments.edit',
    'profile.view',
    'profile.edit',
    'profile.change_password'
);

-- Staff - Limited access
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 
    (SELECT id FROM roles WHERE slug = 'staff'),
    id
FROM permissions
WHERE slug IN (
    'dashboard.view',
    'appointments.view',
    'queue.view',
    'profile.view',
    'profile.edit',
    'profile.change_password'
);

-- Patient - Profile only
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 
    (SELECT id FROM roles WHERE slug = 'patient'),
    id
FROM permissions
WHERE slug IN (
    'dashboard.view',
    'profile.view',
    'profile.edit',
    'profile.change_password'
);
