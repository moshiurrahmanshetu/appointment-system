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
            <li class="breadcrumb-item active" aria-current="page">Edit Consultation</li>
        </ol>
    </nav>
    <h1 class="page-title">Edit Consultation</h1>
    <p class="text-muted">Update consultation details</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Consultation Form</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('consultations/update/' . $consultation['id']) ?>" id="editConsultationForm">
                    <?= csrf_field() ?>
                    
                    <!-- Read-only Information -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Consultation Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Consultation No:</strong> <code><?= e($consultation['consultation_no']) ?></code><br>
                                    <strong>Patient:</strong> <?= e($consultation['patient_name']) ?><br>
                                    <strong>Doctor:</strong> <?= e($consultation['doctor_name']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Created:</strong> <?= date('F j, Y g:i A', strtotime($consultation['created_at'])) ?><br>
                                    <strong>Status:</strong> <span class="badge <?= $consultation['consultation_status'] === 'Completed' ? 'bg-success' : 'bg-warning' ?>"><?= e($consultation['consultation_status']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" id="consultationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button" role="tab">Medical Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="followup-tab" data-bs-toggle="tab" data-bs-target="#followup" type="button" role="tab">Follow-up</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="consultationTabsContent">
                        <!-- Medical Information Tab -->
                        <div class="tab-pane fade show active" id="medical" role="tabpanel">
                            <input type="hidden" name="patient_id" value="<?= $consultation['patient_id'] ?>">
                            <input type="hidden" name="appointment_id" value="<?= $consultation['appointment_id'] ?>">
                            <input type="hidden" name="queue_id" value="<?= $consultation['queue_id'] ?>">
                            
                            <div class="mb-3">
                                <label for="doctor_id" class="form-label">Doctor <span class="text-danger">*</span></label>
                                <select class="form-select <?= has_error('doctor_id') ? 'is-invalid' : '' ?>" 
                                        id="doctor_id" 
                                        name="doctor_id" 
                                        required>
                                    <option value="">Select Doctor</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id'] ?>" <?= old('doctor_id') ?: $consultation['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                        <?= e($doctor['full_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (has_error('doctor_id')): ?>
                                <div class="invalid-feedback"><?= e(error('doctor_id')) ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="visit_type" class="form-label">Visit Type</label>
                                <select class="form-select" id="visit_type" name="visit_type">
                                    <option value="New" <?= (old('visit_type') ?: $consultation['visit_type']) === 'New' ? 'selected' : '' ?>>New Visit</option>
                                    <option value="Follow-up" <?= (old('visit_type') ?: $consultation['visit_type']) === 'Follow-up' ? 'selected' : '' ?>>Follow-up</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="chief_complaint" class="form-label">Chief Complaint</label>
                                <textarea class="form-control" id="chief_complaint" name="chief_complaint" rows="3"><?= e(old('chief_complaint') ?: $consultation['chief_complaint']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="history" class="form-label">History</label>
                                <textarea class="form-control" id="history" name="history" rows="4"><?= e(old('history') ?: $consultation['history']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="physical_examination" class="form-label">Physical Examination</label>
                                <textarea class="form-control" id="physical_examination" name="physical_examination" rows="4"><?= e(old('physical_examination') ?: $consultation['physical_examination']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="diagnosis" class="form-label">Diagnosis</label>
                                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"><?= e(old('diagnosis') ?: $consultation['diagnosis']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="doctor_notes" class="form-label">Doctor Notes</label>
                                <textarea class="form-control" id="doctor_notes" name="doctor_notes" rows="4"><?= e(old('doctor_notes') ?: $consultation['doctor_notes']) ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Follow-up Tab -->
                        <div class="tab-pane fade" id="followup" role="tabpanel">
                            <div class="mb-3">
                                <label for="consultation_status" class="form-label">Consultation Status</label>
                                <select class="form-select" id="consultation_status" name="consultation_status">
                                    <option value="Draft" <?= (old('consultation_status') ?: $consultation['consultation_status']) === 'Draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="Completed" <?= (old('consultation_status') ?: $consultation['consultation_status']) === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <?php if ($consultation['consultation_status'] === 'Completed' && !can('consultation.complete')): ?>
                                <small class="text-muted">Note: Consultation is already completed. Requires special permission to edit.</small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="follow_up_required" class="form-label">Follow-up Required</label>
                                <select class="form-select" id="follow_up_required" name="follow_up_required">
                                    <option value="No" <?= (old('follow_up_required') ?: $consultation['follow_up_required']) === 'No' ? 'selected' : '' ?>>No</option>
                                    <option value="Yes" <?= (old('follow_up_required') ?: $consultation['follow_up_required']) === 'Yes' ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="follow_up_date" 
                                       name="follow_up_date" 
                                       value="<?= e(old('follow_up_date') ?: $consultation['follow_up_date']) ?>"
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Consultation
                        </button>
                        <a href="<?= url('consultations/show/' . $consultation['id']) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Patient Name</label>
                    <div class="fw-bold"><?= e($consultation['patient_name']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Patient Code</label>
                    <div class="fw-bold"><code><?= e($consultation['patient_code']) ?></code></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Phone</label>
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
            </div>
        </div>
    </div>
</div>