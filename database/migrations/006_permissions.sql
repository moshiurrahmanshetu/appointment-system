-- Comprehensive permissions for RBAC system
-- Clear existing permissions and insert new comprehensive set

-- Clear existing permissions
TRUNCATE TABLE `permissions`;

-- Clear existing role permissions
TRUNCATE TABLE `role_permissions`;

-- Insert comprehensive permissions
INSERT INTO `permissions` (`name`, `slug`, `description`) VALUES
-- Dashboard
('View Dashboard', 'dashboard.view', 'Access to dashboard'),

-- Users
('View Users', 'users.view', 'View list of users'),
('Create Users', 'users.create', 'Create new users'),
('Edit Users', 'users.edit', 'Edit existing users'),
('Delete Users', 'users.delete', 'Delete users'),
('Restore Users', 'users.restore', 'Restore deleted users'),

-- Patients
('View Patients', 'patients.view', 'View list of patients'),
('Create Patients', 'patients.create', 'Create new patients'),
('Edit Patients', 'patients.edit', 'Edit existing patients'),
('Delete Patients', 'patients.delete', 'Delete patients'),
('Restore Patients', 'patients.restore', 'Restore deleted patients'),

-- Doctors
('View Doctors', 'doctors.view', 'View list of doctors'),
('Create Doctors', 'doctors.create', 'Create new doctors'),
('Edit Doctors', 'doctors.edit', 'Edit existing doctors'),
('Delete Doctors', 'doctors.delete', 'Delete doctors'),

-- Appointments
('View Appointments', 'appointments.view', 'View list of appointments'),
('Create Appointments', 'appointments.create', 'Create new appointments'),
('Edit Appointments', 'appointments.edit', 'Edit existing appointments'),
('Delete Appointments', 'appointments.delete', 'Delete appointments'),

-- Queue
('View Queue', 'queue.view', 'View appointment queue'),
('Manage Queue', 'queue.manage', 'Manage appointment queue status'),

-- Reports
('View Reports', 'reports.view', 'Access to system reports'),

-- Settings
('View Settings', 'settings.view', 'View system settings'),
('Edit Settings', 'settings.edit', 'Edit system settings'),

-- Profile
('View Profile', 'profile.view', 'View own profile'),
('Edit Profile', 'profile.edit', 'Edit own profile'),
('Change Password', 'profile.change_password', 'Change own password'),

-- Roles & Permissions (Super Admin only)
('View Roles', 'roles.view', 'View roles and permissions'),
('Manage Roles', 'roles.manage', 'Manage roles and permissions assignment');
