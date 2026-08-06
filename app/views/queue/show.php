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
            <li class="breadcrumb-item"><a href="<?= url('queue') ?>">Queue</a></li>
            <li class="breadcrumb-item active" aria-current="page">Queue Details</li>
        </ol>
    </nav>
    <h1 class="page-title">Queue Details</h1>
    <p class="text-muted">View queue entry information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Queue Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Queue Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Token Number</label>
                        <div class="fw-bold"><span class="badge bg-primary fs-5"><?= e($queue['token_no']) ?></span></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Appointment No</label>
                        <div class="fw-bold"><code><?= e($queue['appointment_no']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Name</label>
                        <div class="fw-bold"><?= e($queue['patient_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Code</label>
                        <div class="fw-bold"><code><?= e($queue['patient_code']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Doctor Name</label>
                        <div class="fw-bold"><?= e($queue['doctor_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Doctor ID</label>
                        <div class="fw-bold"><code><?= e($queue['doctor_user_id']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Queue Date</label>
                        <div class="fw-bold"><?= date('F j, Y', strtotime($queue['queue_date'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Appointment Time</label>
                        <div class="fw-bold"><?= date('g:i A', strtotime($queue['appointment_time'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Priority</label>
                        <div class="fw-bold">
                            <?php
                            $priorityClass = match($queue['priority']) {
                                'Normal' => 'bg-secondary',
                                'Urgent' => 'bg-warning',
                                'Emergency' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $priorityClass ?>"><?= e($queue['priority']) ?></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Queue Status</label>
                        <div class="fw-bold">
                            <?php
                            $statusClass = match($queue['queue_status']) {
                                'Waiting' => 'bg-warning',
                                'Called' => 'bg-info',
                                'With Doctor' => 'bg-success',
                                'Completed' => 'bg-primary',
                                'Skipped' => 'bg-secondary',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?> fs-5"><?= e($queue['queue_status']) ?></span>
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1">Remarks</label>
                        <div class="fw-bold"><?= $queue['remarks'] ? e($queue['remarks']) : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Timeline Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Queue Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker <?= $queue['created_at'] ? 'bg-primary' : 'bg-secondary' ?>"></div>
                        <div class="timeline-content">
                            <h6 class="fw-bold">Queue Created</h6>
                            <p class="text-muted small mb-0"><?= $queue['created_at'] ? date('F j, Y g:i A', strtotime($queue['created_at'])) : 'N/A' ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker <?= $queue['called_at'] ? 'bg-info' : 'bg-secondary' ?>"></div>
                        <div class="timeline-content">
                            <h6 class="fw-bold">Patient Called</h6>
                            <p class="text-muted small mb-0"><?= $queue['called_at'] ? date('F j, Y g:i A', strtotime($queue['called_at'])) : 'Not called yet' ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker <?= $queue['started_at'] ? 'bg-success' : 'bg-secondary' ?>"></div>
                        <div class="timeline-content">
                            <h6 class="fw-bold">Consultation Started</h6>
                            <p class="text-muted small mb-0"><?= $queue['started_at'] ? date('F j, Y g:i A', strtotime($queue['started_at'])) : 'Not started yet' ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker <?= $queue['completed_at'] ? 'bg-primary' : 'bg-secondary' ?>"></div>
                        <div class="timeline-content">
                            <h6 class="fw-bold">Completed</h6>
                            <p class="text-muted small mb-0"><?= $queue['completed_at'] ? date('F j, Y g:i A', strtotime($queue['completed_at'])) : 'Not completed yet' ?></p>
                        </div>
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
                    <?php if (can('queue.call') && $queue['queue_status'] === 'Waiting'): ?>
                    <a href="<?= url('queue/call/' . $queue['id']) ?>" class="btn btn-success" onclick="return confirm('Call this patient?')">
                        <i class="bi bi-megaphone me-2"></i>Call Patient
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('queue.manage') && $queue['queue_status'] === 'Called'): ?>
                    <a href="<?= url('queue/start/' . $queue['id']) ?>" class="btn btn-info" onclick="return confirm('Start consultation?')">
                        <i class="bi bi-play-circle me-2"></i>Start Consultation
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('queue.complete') && $queue['queue_status'] === 'With Doctor'): ?>
                    <a href="<?= url('queue/complete/' . $queue['id']) ?>" class="btn btn-primary" onclick="return confirm('Complete consultation?')">
                        <i class="bi bi-check-circle me-2"></i>Complete Consultation
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('queue.skip') && in_array($queue['queue_status'], ['Called', 'Waiting'])): ?>
                    <a href="<?= url('queue/skip/' . $queue['id']) ?>" class="btn btn-warning" onclick="return confirm('Skip this patient?')">
                        <i class="bi bi-skip-forward me-2"></i>Skip Patient
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('queue.cancel') && in_array($queue['queue_status'], ['Waiting', 'Called', 'With Doctor'])): ?>
                    <a href="<?= url('queue/cancel/' . $queue['id']) ?>" class="btn btn-danger" onclick="return confirm('Cancel this queue entry?')">
                        <i class="bi bi-x-circle me-2"></i>Cancel Queue
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Patient Contact Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Patient Contact</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Patient Phone</label>
                    <div class="fw-bold"><?= e($queue['patient_phone']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Gender</label>
                    <div class="fw-bold"><?= e($queue['patient_gender']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Date of Birth</label>
                    <div class="fw-bold"><?= date('F j, Y', strtotime($queue['patient_dob'])) ?></div>
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
                    <div class="fw-bold"><?= $queue['created_by_name'] ? e($queue['created_by_name']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Updated By</label>
                    <div class="fw-bold"><?= $queue['updated_by_name'] ? e($queue['updated_by_name']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Created Date</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($queue['created_at'])) ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Last Updated</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($queue['updated_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= url('queue') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Queue
    </a>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #6c757d;
}

.timeline-content {
    padding-left: 10px;
}
</style>