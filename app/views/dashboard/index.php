<?php if (has_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e(flash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e(flash('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <i class="bi bi-calendar"></i>
            This week
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Patients</h5>
                        <h2 class="display-4">0</h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Appointments Today</h5>
                        <h2 class="display-4">0</h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">In Queue</h5>
                        <h2 class="display-4">0</h2>
                    </div>
                    <i class="bi bi-list-ol fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="display-4">1</h2>
                    </div>
                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted text-center py-5">
                    <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                    No recent activity to display
                </p>
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
                    <button class="btn btn-outline-primary" disabled>
                        <i class="bi bi-plus-circle me-2"></i>New Appointment
                    </button>
                    <button class="btn btn-outline-success" disabled>
                        <i class="bi bi-person-plus me-2"></i>Add Patient
                    </button>
                    <button class="btn btn-outline-info" disabled>
                        <i class="bi bi-clipboard-data me-2"></i>View Reports
                    </button>
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="bi bi-gear me-2"></i>Settings
                    </button>
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
                                <th>User ID:</th>
                                <td><?= e($user['user_id']) ?></td>
                            </tr>
                            <tr>
                                <th>Timezone:</th>
                                <td><?= config('timezone') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
