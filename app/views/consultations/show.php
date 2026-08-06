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
            <li class="breadcrumb-item"><a href="<?= url('consultations') ?>">Consultations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Consultation Details</li>
        </ol>
    </nav>
    <h1 class="page-title">Consultation Details</h1>
    <p class="text-muted">View consultation information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Consultation Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Consultation Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Consultation No</label>
                        <div class="fw-bold"><code><?= e($consultation['consultation_no']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Status</label>
                        <div class="fw-bold">
                            <?php
                            $statusClass = $consultation['consultation_status'] === 'Completed' ? 'bg-success' : 'bg-warning';
                            ?>
                            <span class="badge <?= $statusClass ?> fs-5"><?= e($consultation['consultation_status']) ?></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Name</label>
                        <div class="fw-bold"><?= e($consultation['patient_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Patient Code</label>
                        <div class="fw-bold"><code><?= e($consultation['patient_code']) ?></code></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Doctor Name</label>
                        <div class="fw-bold"><?= e($consultation['doctor_name']) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Visit Type</label>
                        <div class="fw-bold"><span class="badge bg-secondary"><?= e($consultation['visit_type']) ?></span></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Consultation Date</label>
                        <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($consultation['created_at'])) ?></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Follow-up Required</label>
                        <div class="fw-bold">
                            <?php if ($consultation['follow_up_required'] === 'Yes'): ?>
                            <span class="badge bg-info">Yes</span>
                            <?php else: ?>
                            <span class="text-muted">No</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($consultation['follow_up_date']): ?>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small mb-1">Follow-up Date</label>
                        <div class="fw-bold"><?= date('F j, Y', strtotime($consultation['follow_up_date'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Medical Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Medical Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Chief Complaint</label>
                    <div class="fw-bold"><?= $consultation['chief_complaint'] ? e($consultation['chief_complaint']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">History</label>
                    <div class="fw-bold"><?= $consultation['history'] ? e($consultation['history']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Physical Examination</label>
                    <div class="fw-bold"><?= $consultation['physical_examination'] ? e($consultation['physical_examination']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Diagnosis</label>
                    <div class="fw-bold"><?= $consultation['diagnosis'] ? e($consultation['diagnosis']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Doctor Notes</label>
                    <div class="fw-bold"><?= $consultation['doctor_notes'] ? e($consultation['doctor_notes']) : 'N/A' ?></div>
                </div>
            </div>
        </div>
        
        <!-- Previous Consultation History -->
        <?php if (!empty($history)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Previous Consultation History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Consultation No</th>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $prev): ?>
                            <tr>
                                <td><code><?= e($prev['consultation_no']) ?></code></td>
                                <td><?= date('M j, Y', strtotime($prev['created_at'])) ?></td>
                                <td><?= e($prev['doctor_name']) ?></td>
                                <td><?= $prev['diagnosis'] ? e(substr($prev['diagnosis'], 0, 50)) . '...' : 'N/A' ?></td>
                                <td>
                                    <a href="<?= url('consultations/show/' . $prev['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <?php if (can('consultation.edit')): ?>
                    <a href="<?= url('consultations/edit/' . $consultation['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Consultation
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('consultation.complete') && $consultation['consultation_status'] === 'Draft'): ?>
                    <a href="<?= url('consultations/complete/' . $consultation['id']) ?>" class="btn btn-success" onclick="return confirm('Complete this consultation?')">
                        <i class="bi bi-check-circle me-2"></i>Complete Consultation
                    </a>
                    <?php endif; ?>
                    
                    <?php if (can('consultation.delete')): ?>
                    <a href="<?= url('consultations/delete/' . $consultation['id']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this consultation?')">
                        <i class="bi bi-trash me-2"></i>Delete Consultation
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
                    <div class="fw-bold"><?= e($consultation['patient_phone']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Gender</label>
                    <div class="fw-bold"><?= e($consultation['patient_gender']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Date of Birth</label>
                    <div class="fw-bold"><?= date('F j, Y', strtotime($consultation['patient_dob'])) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Blood Group</label>
                    <div class="fw-bold"><?= e($consultation['patient_blood_group']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Address</label>
                    <div class="fw-bold"><?= e($consultation['patient_address']) ?></div>
                </div>
            </div>
        </div>
        
        <!-- Visit Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Visit Information</h5>
            </div>
            <div class="card-body">
                <?php if ($consultation['appointment_no']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Appointment No</label>
                    <div class="fw-bold"><code><?= e($consultation['appointment_no']) ?></code></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consultation['token_no']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Token No</label>
                    <div class="fw-bold"><span class="badge bg-primary"><?= e($consultation['token_no']) ?></span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consultation['appointment_date']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Appointment Date</label>
                    <div class="fw-bold"><?= date('F j, Y', strtotime($consultation['appointment_date'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consultation['appointment_time']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Appointment Time</label>
                    <div class="fw-bold"><?= date('g:i A', strtotime($consultation['appointment_time'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consultation['priority']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Priority</label>
                    <div class="fw-bold">
                        <?php
                        $priorityClass = match($consultation['priority']) {
                            'Normal' => 'bg-secondary',
                            'Urgent' => 'bg-warning',
                            'Emergency' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $priorityClass ?>"><?= e($consultation['priority']) ?></span>
                    </div>
                </div>
                <?php endif; ?>
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
                    <div class="fw-bold"><?= $consultation['created_by_name'] ? e($consultation['created_by_name']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Updated By</label>
                    <div class="fw-bold"><?= $consultation['updated_by_name'] ? e($consultation['updated_by_name']) : 'N/A' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Created Date</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($consultation['created_at'])) ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Last Updated</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($consultation['updated_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= url('consultations') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Consultations
    </a>
</div>