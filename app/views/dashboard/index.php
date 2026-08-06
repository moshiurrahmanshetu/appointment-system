<?php if (has_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= e(flash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= e(flash('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="page-header mb-4">
    <h1 class="page-title">Dashboard</h1>
    <p class="text-muted">Welcome back, <?= e($user['full_name']) ?>! (<?= e($user['role']['name']) ?>)</p>
</div>

<!-- Permission-based Dashboard Cards -->
<div class="row">
    <?php if (can('patients.view')): ?>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title opacity-75">Total Patients</h5>
                        <h2 class="display-4 fw-bold"><?= $patientCount ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (can('appointments.view')): ?>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title opacity-75">Total Appointments</h5>
                        <h2 class="display-4 fw-bold"><?= $appointmentCount ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (can('queue.view')): ?>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title opacity-75">Patients Waiting</h5>
                        <h2 class="display-4 fw-bold"><?= $queueStats['waiting'] ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-list-ol fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (can('queue.view')): ?>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title opacity-75">With Doctor</h5>
                        <h2 class="display-4 fw-bold"><?= $queueStats['with_doctor'] ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Queue Stats Row -->
<?php if (can('queue.view')): ?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-primary">Completed Today</h5>
                        <h3 class="display-6 fw-bold"><?= $queueStats['completed'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-check-circle text-primary fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-secondary">Skipped Today</h5>
                        <h3 class="display-6 fw-bold"><?= $queueStats['skipped'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-skip-forward text-secondary fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-success">Called</h5>
                        <h3 class="display-6 fw-bold"><?= $queueStats['called'] ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-megaphone text-success fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="bi bi-clock-history text-muted fs-1 d-block mb-3"></i>
                    <p class="text-muted">No recent activity to display</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (can('appointments.create')): ?>
                    <a href="<?= url('appointments/create') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>New Appointment
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('patients.create')): ?>
                    <a href="<?= url('patients/create') ?>" class="btn btn-outline-success">
                        <i class="bi bi-person-plus me-2"></i>Add Patient
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('queue.view')): ?>
                    <a href="<?= url('queue') ?>" class="btn btn-outline-warning">
                        <i class="bi bi-list-ol me-2"></i>View Queue
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('reports.view')): ?>
                    <button class="btn btn-outline-info" disabled>
                        <i class="bi bi-clipboard-data me-2"></i>View Reports
                    </button>
                    <?php endif; ?>
                    
                    <?php if (can('settings.edit')): ?>
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="bi bi-gear me-2"></i>Settings
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Application Name:</th>
                                <td><?= config('name') ?></td>
                            </tr>
                            <tr>
                                <th>Version:</th>
                                <td><?= config('version') ?></td>
                            </tr>
                            <tr>
                                <th>PHP Version:</th>
                                <td><?= PHP_VERSION ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Current User:</th>
                                <td><?= e($user['full_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td><?= e($user['role']['name']) ?></td>
                            </tr>
                            <tr>
                                <th>Permissions:</th>
                                <td><?= count($user['permissions']) ?> permissions</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
