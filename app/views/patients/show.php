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
            <li class="breadcrumb-item"><a href="<?= url('patients') ?>">Patients</a></li>
            <li class="breadcrumb-item active" aria-current="page">Patient Profile</li>
        </ol>
    </nav>
    <h1 class="page-title">Patient Profile</h1>
    <p class="text-muted">View patient details and information</p>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Patient Photo Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <?php if ($patient['photo']): ?>
                <img src="<?= asset($patient['photo']) ?>" alt="<?= e($patient['full_name']) ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;">
                <?php else: ?>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px; font-size: 3rem;">
                    <?= strtoupper(substr($patient['full_name'], 0, 1)) ?>
                </div>
                <?php endif; ?>
                <h4 class="mb-1"><?= e($patient['full_name']) ?></h4>
                <p class="text-muted mb-3"><?= e($patient['patient_code']) ?></p>
                
                <?php
                $statusClass = match($patient['status']) {
                    'active' => 'bg-success',
                    'inactive' => 'bg-warning',
                    'blocked' => 'bg-danger',
                    default => 'bg-secondary'
                };
                ?>
                <span class="badge <?= $statusClass ?> mb-3"><?= ucfirst($patient['status']) ?></span>
                
                <div class="d-flex gap-2 justify-content-center">
                    <?php if (can('patients.edit')): ?>
                    <a href="<?= url('patients/edit/' . $patient['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('patients.view')): ?>
                    <a href="<?= url('patients/slip/' . $patient['id']) ?>" class="btn btn-info">
                        <i class="bi bi-printer me-2"></i>Print Slip
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Account Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <?php if ($patient['user_id']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Login User ID</label>
                    <div class="fw-bold">
                        <?php
                        $linkedUser = $patient['login_id'] ?? null;
                        echo $linkedUser ? e($linkedUser) : 'N/A';
                        ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Account Status</label>
                    <div>
                        <?php
                        $accountStatus = $patient['account_status'] ?? 'unknown';
                        $accountStatusClass = match($accountStatus) {
                            'active' => 'bg-success',
                            'inactive' => 'bg-warning',
                            'blocked' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $accountStatusClass ?>"><?= ucfirst($accountStatus) ?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center text-muted">
                    <i class="bi bi-person-x fs-1 mb-2"></i>
                    <p>No linked account</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Basic Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Code</label>
                        <div class="fw-bold"><code><?= e($patient['patient_code']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Full Name</label>
                        <div class="fw-bold"><?= e($patient['full_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Phone</label>
                        <div class="fw-bold"><?= e($patient['phone']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Gender</label>
                        <div class="fw-bold"><?= ucfirst($patient['gender']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Date of Birth</label>
                        <div class="fw-bold"><?= date('F j, Y', strtotime($patient['dob'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Blood Group</label>
                        <div class="fw-bold"><?= $patient['blood_group'] ? e($patient['blood_group']) : 'N/A' ?></div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="text-muted small mb-1">Address</label>
                        <div class="fw-bold"><?= $patient['address'] ? e($patient['address']) : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Emergency Contact Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Emergency Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Contact Name</label>
                        <div class="fw-bold"><?= $patient['emergency_contact'] ? e($patient['emergency_contact']) : 'N/A' ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Contact Phone</label>
                        <div class="fw-bold"><?= $patient['emergency_phone'] ? e($patient['emergency_phone']) : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Status</label>
                        <div>
                            <?php
                            $statusClass = match($patient['status']) {
                                'active' => 'bg-success',
                                'inactive' => 'bg-warning',
                                'blocked' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($patient['status']) ?></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Created By</label>
                        <div class="fw-bold"><?= $created_by ? e($created_by['full_name']) : 'N/A' ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Created Date</label>
                        <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($patient['created_at'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Last Updated</label>
                        <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($patient['updated_at'])) ?></div>
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
                    <?php if (can('patients.edit')): ?>
                    <a href="<?= url('patients/edit/' . $patient['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Patient
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('patients.view')): ?>
                    <a href="<?= url('patients/slip/' . $patient['id']) ?>" class="btn btn-info">
                        <i class="bi bi-printer me-2"></i>Print Registration Slip
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('patients.status')): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Change Status
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= url('patients/status/' . $patient['id']) ?>?status=active">
                                    <i class="bi bi-check-circle me-2"></i>Active
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('patients/status/' . $patient['id']) ?>?status=inactive">
                                    <i class="bi bi-pause-circle me-2"></i>Inactive
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('patients/status/' . $patient['id']) ?>?status=blocked">
                                    <i class="bi bi-x-circle me-2"></i>Blocked
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (can('patients.delete')): ?>
                    <a href="<?= url('patients/delete/' . $patient['id']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this patient? This will also disable their login account.')">
                        <i class="bi bi-trash me-2"></i>Delete Patient
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= url('patients') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patients
    </a>
</div>
