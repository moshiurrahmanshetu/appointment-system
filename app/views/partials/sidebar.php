<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : '' ?>" href="<?= url('profile') ?>">
                    <i class="bi bi-person me-2"></i>My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">
                    <i class="bi bi-people me-2"></i>Patients
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">
                    <i class="bi bi-calendar-event me-2"></i>Appointments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">
                    <i class="bi bi-list-ol me-2"></i>Queue
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">
                    <i class="bi bi-people-fill me-2"></i>Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">
                    <i class="bi bi-gear me-2"></i>Settings
                </a>
            </li>
        </ul>
    </div>
</div>
