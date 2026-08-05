<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="bi bi-hospital"></i>
            <span class="sidebar-brand-text"><?= config('name') ?></span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <?php if (can('dashboard.view')): ?>
            <li class="nav-item">
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>" 
                   href="<?= url('dashboard') ?>" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Dashboard">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('profile.view')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : '' ?>" 
                   href="<?= url('profile') ?>" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="My Profile">
                    <i class="bi bi-person nav-icon"></i>
                    <span class="nav-text">My Profile</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('patients.view')): ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="#" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Patients">
                    <i class="bi bi-people nav-icon"></i>
                    <span class="nav-text">Patients</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('appointments.view')): ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="#" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Appointments">
                    <i class="bi bi-calendar-event nav-icon"></i>
                    <span class="nav-text">Appointments</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('queue.view')): ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="#" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Queue">
                    <i class="bi bi-list-ol nav-icon"></i>
                    <span class="nav-text">Queue</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('users.view')): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'active' : '' ?>" 
                   href="<?= url('users') ?>" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Users">
                    <i class="bi bi-people-fill nav-icon"></i>
                    <span class="nav-text">Users</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('doctors.view')): ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="#" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Doctors">
                    <i class="bi bi-person-badge nav-icon"></i>
                    <span class="nav-text">Doctors</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('reports.view')): ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="#" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Reports">
                    <i class="bi bi-clipboard-data nav-icon"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (can('settings.view')): ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="#" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Settings">
                    <i class="bi bi-gear nav-icon"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>
