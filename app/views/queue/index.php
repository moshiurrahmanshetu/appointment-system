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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Queue Management</li>
        </ol>
    </nav>
    <h1 class="page-title">Queue Management</h1>
    <p class="text-muted">Manage patient queue and consultation flow</p>
</div>

<!-- Dashboard Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people fs-2 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Waiting</h5>
                        <h3 class="mb-0"><?= $stats['waiting'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-person-badge fs-2 text-success"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">With Doctor</h5>
                        <h3 class="mb-0"><?= $stats['with_doctor'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Completed Today</h5>
                        <h3 class="mb-0"><?= $stats['completed'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-secondary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-x-circle fs-2 text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Skipped Today</h5>
                        <h3 class="mb-0"><?= $stats['skipped'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">Queue List</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if (can('queue.call') && $doctors): ?>
                <form method="POST" action="<?= url('queue/call-next') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <?php if (!$filters['doctor_id']): ?>
                    <select name="doctor_id" class="form-select d-inline-block w-auto me-2" required>
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= $doctor['id'] ?>"><?= e($doctor['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-megaphone me-2"></i>Call Next
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="<?= url('queue') ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Search by Token, Appointment No, Patient..." 
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <?php if ($doctors): ?>
                <div class="col-md-2">
                    <select class="form-select" name="doctor_id">
                        <option value="">All Doctors</option>
                        <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= $doctor['id'] ?>" <?= $filters['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                            <?= e($doctor['full_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-2">
                    <input type="date" 
                           class="form-control" 
                           name="queue_date" 
                           value="<?= e($filters['queue_date']) ?>"
                           placeholder="Filter by Date">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="queue_status">
                        <option value="">All Status</option>
                        <option value="Waiting" <?= $filters['queue_status'] === 'Waiting' ? 'selected' : '' ?>>Waiting</option>
                        <option value="Called" <?= $filters['queue_status'] === 'Called' ? 'selected' : '' ?>>Called</option>
                        <option value="With Doctor" <?= $filters['queue_status'] === 'With Doctor' ? 'selected' : '' ?>>With Doctor</option>
                        <option value="Completed" <?= $filters['queue_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Skipped" <?= $filters['queue_status'] === 'Skipped' ? 'selected' : '' ?>>Skipped</option>
                        <option value="Cancelled" <?= $filters['queue_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Queue Table -->
        <?php if (!empty($queue)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Appointment No</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Time</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($queue as $q): ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary fs-6"><?= e($q['token_no']) ?></span>
                        </td>
                        <td>
                            <code><?= e($q['appointment_no']) ?></code>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= e($q['patient_name']) ?></div>
                                <div class="text-muted small"><?= e($q['patient_code']) ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold"><?= e($q['doctor_name']) ?></div>
                        </td>
                        <td>
                            <?= date('g:i A', strtotime($q['appointment_time'])) ?>
                        </td>
                        <td>
                            <?php
                            $priorityClass = match($q['priority']) {
                                'Normal' => 'bg-secondary',
                                'Urgent' => 'bg-warning',
                                'Emergency' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $priorityClass ?>"><?= e($q['priority']) ?></span>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($q['queue_status']) {
                                'Waiting' => 'bg-warning',
                                'Called' => 'bg-info',
                                'With Doctor' => 'bg-success',
                                'Completed' => 'bg-primary',
                                'Skipped' => 'bg-secondary',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= e($q['queue_status']) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if (can('queue.view')): ?>
                                <a href="<?= url('queue/show/' . $q['id']) ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('queue.call') && $q['queue_status'] === 'Waiting'): ?>
                                <a href="<?= url('queue/call/' . $q['id']) ?>" class="btn btn-sm btn-outline-success" title="Call" onclick="return confirm('Call this patient?')">
                                    <i class="bi bi-megaphone"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('queue.manage') && $q['queue_status'] === 'Called'): ?>
                                <a href="<?= url('queue/start/' . $q['id']) ?>" class="btn btn-sm btn-outline-info" title="Start Consultation" onclick="return confirm('Start consultation?')">
                                    <i class="bi bi-play-circle"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('queue.complete') && $q['queue_status'] === 'With Doctor'): ?>
                                <a href="<?= url('queue/complete/' . $q['id']) ?>" class="btn btn-sm btn-outline-success" title="Complete" onclick="return confirm('Complete consultation?')">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('queue.skip') && in_array($q['queue_status'], ['Called', 'Waiting'])): ?>
                                <a href="<?= url('queue/skip/' . $q['id']) ?>" class="btn btn-sm btn-outline-warning" title="Skip" onclick="return confirm('Skip this patient?')">
                                    <i class="bi bi-skip-forward"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('queue.cancel') && in_array($q['queue_status'], ['Waiting', 'Called', 'With Doctor'])): ?>
                                <a href="<?= url('queue/cancel/' . $q['id']) ?>" class="btn btn-sm btn-outline-danger" title="Cancel" onclick="return confirm('Cancel this queue entry?')">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted mb-3"></i>
            <p class="text-muted">No queue entries found.</p>
        </div>
        <?php endif; ?>
    </div>
</div>