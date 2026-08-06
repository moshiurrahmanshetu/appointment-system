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
            <li class="breadcrumb-item"><a href="<?= url('appointments') ?>">Appointments</a></li>
            <li class="breadcrumb-item active" aria-current="page">Appointment Details</li>
        </ol>
    </nav>
    <h1 class="page-title">Appointment Details</h1>
    <p class="text-muted">View appointment information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Appointment Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Appointment Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Appointment No</label>
                        <div class="fw-bold"><code><?= e($appointment['appointment_no']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Serial No</label>
                        <div class="fw-bold"><span class="badge bg-primary">#<?= $appointment['serial_no'] ?></span></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Name</label>
                        <div class="fw-bold"><?= e($appointment['patient_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Code</label>
                        <div class="fw-bold"><code><?= e($appointment['patient_code']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Doctor Name</label>
                        <div class="fw-bold"><?= e($appointment['doctor_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Doctor ID</label>
                        <div class="fw-bold"><code><?= e($appointment['doctor_user_id']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Appointment Date</label>
                        <div class="fw-bold"><?= date('F j, Y', strtotime($appointment['appointment_date'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Appointment Time</label>
                        <div class="fw-bold"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Visit Type</label>
                        <div class="fw-bold"><?= e($appointment['visit_type']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Priority</label>
                        <div class="fw-bold">
                            <?php
                            $priorityClass = match($appointment['priority']) {
                                'Normal' => 'bg-secondary',
                                'Urgent' => 'bg-warning',
                                'Emergency' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $priorityClass ?>"><?= e($appointment['priority']) ?></span>
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1">Remarks</label>
                        <div class="fw-bold"><?= $appointment['remarks'] ? e($appointment['remarks']) : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <?php if (can('appointments.edit')): ?>
                    <a href="<?= url('appointments/edit/' . $appointment['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Appointment
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('appointments.edit')): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Change Status
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Pending">
                                    <i class="bi bi-clock me-2"></i>Pending
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Confirmed">
                                    <i class="bi bi-check-circle me-2"></i>Confirmed
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Checked In">
                                    <i class="bi bi-person-check me-2"></i>Checked In
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=In Queue">
                                    <i class="bi bi-list-ol me-2"></i>In Queue
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=With Doctor">
                                    <i class="bi bi-person-badge me-2"></i>With Doctor
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Completed">
                                    <i class="bi bi-check2-circle me-2"></i>Completed
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Cancelled">
                                    <i class="bi bi-x-circle me-2"></i>Cancelled
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (can('appointments.delete')): ?>
                    <a href="<?= url('appointments/delete/' . $appointment['id']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?')">
                        <i class="bi bi-trash me-2"></i>Delete Appointment
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Status</h5>
            </div>
            <div class="card-body text-center">
                <?php
                $statusClass = match($appointment['status']) {
                    'Pending' => 'bg-warning',
                    'Confirmed' => 'bg-info',
                    'Checked In' => 'bg-primary',
                    'In Queue' => 'bg-secondary',
                    'With Doctor' => 'bg-success',
                    'Completed' => 'bg-success',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
                ?>
                <span class="badge <?= $statusClass ?> fs-4"><?= e($appointment['status']) ?></span>
            </div>
        </div>
        
        <!-- Patient Contact Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Patient Contact</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Patient Phone</label>
                    <div class="fw-bold"><?= e($appointment['patient_phone']) ?></div>
                </div>
            </div>
        </div>
        
        <!-- System Information Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Created By</label>
                    <div class="fw-bold"><?= $appointment['created_by_name'] ? e($appointment['created_by_name']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Updated By</label>
                    <div class="fw-bold"><?= $appointment['updated_by_name'] ? e($appointment['updated_by_name']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Created Date</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($appointment['created_at'])) ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Last Updated</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($appointment['updated_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= url('appointments') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Appointments
    </a>
</div>