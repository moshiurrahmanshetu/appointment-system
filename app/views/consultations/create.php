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
            <li class="breadcrumb-item active" aria-current="page">New Consultation</li>
        </ol>
    </nav>
    <h1 class="page-title">New Consultation</h1>
    <p class="text-muted">Record patient consultation details</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Consultation Form</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('consultations') ?>" id="createConsultationForm">
                    <?= csrf_field() ?>
                    
                    <!-- Patient Summary Card -->
                    <?php if ($patient): ?>
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Patient Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> <?= e($patient['full_name']) ?><br>
                                    <strong>Code:</strong> <code><?= e($patient['patient_code']) ?></code><br>
                                    <strong>Phone:</strong> <?= e($patient['phone']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Gender:</strong> <?= e($patient['gender']) ?><br>
                                    <strong>DOB:</strong> <?= date('F j, Y', strtotime($patient['dob'])) ?><br>
                                    <strong>Blood Group:</strong> <?= e($patient['blood_group']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Queue/Appointment Summary -->
                    <?php if ($queue || $appointment): ?>
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Visit Information</h6>
                            <?php if ($queue): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Token:</strong> <span class="badge bg-primary"><?= e($queue['token_no']) ?></span><br>
                                    <strong>Queue Status:</strong> <span class="badge bg-success"><?= e($queue['queue_status']) ?></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Appointment No:</strong> <code><?= e($queue['appointment_no']) ?></code><br>
                                    <strong>Priority:</strong> <span class="badge bg-secondary"><?= e($queue['priority']) ?></span>
                                </div>
                            </div>
                            <?php elseif ($appointment): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Appointment No:</strong> <code><?= e($appointment['appointment_no']) ?></code><br>
                                    <strong>Status:</strong> <span class="badge bg-secondary"><?= e($appointment['status']) ?></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Date:</strong> <?= date('F j, Y', strtotime($appointment['appointment_date'])) ?><br>
                                    <strong>Time:</strong> <?= date('g:i A', strtotime($appointment['appointment_time'])) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
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
                            <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?? '' ?>">
                            <input type="hidden" name="queue_id" value="<?= $queue['id'] ?? '' ?>">
                            
                            <div class="mb-3">
                                <label for="doctor_id" class="form-label">Doctor <span class="text-danger">*</span></label>
                                <select class="form-select <?= has_error('doctor_id') ? 'is-invalid' : '' ?>" 
                                        id="doctor_id" 
                                        name="doctor_id" 
                                        required>
                                    <option value="">Select Doctor</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id'] ?>" <?= old('doctor_id') ?: ($queue['doctor_id'] ?? '') == $doctor['id'] ? 'selected' : '' ?>>
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
                                    <option value="New" <?= old('visit_type') === 'New' ? 'selected' : '' ?>>New Visit</option>
                                    <option value="Follow-up" <?= old('visit_type') === 'Follow-up' ? 'selected' : '' ?>>Follow-up</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="chief_complaint" class="form-label">Chief Complaint</label>
                                <textarea class="form-control" id="chief_complaint" name="chief_complaint" rows="3"><?= e(old('chief_complaint')) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="history" class="form-label">History</label>
                                <textarea class="form-control" id="history" name="history" rows="4"><?= e(old('history')) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="physical_examination" class="form-label">Physical Examination</label>
                                <textarea class="form-control" id="physical_examination" name="physical_examination" rows="4"><?= e(old('physical_examination')) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="diagnosis" class="form-label">Diagnosis</label>
                                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"><?= e(old('diagnosis')) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="doctor_notes" class="form-label">Doctor Notes</label>
                                <textarea class="form-control" id="doctor_notes" name="doctor_notes" rows="4"><?= e(old('doctor_notes')) ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Follow-up Tab -->
                        <div class="tab-pane fade" id="followup" role="tabpanel">
                            <div class="mb-3">
                                <label for="follow_up_required" class="form-label">Follow-up Required</label>
                                <select class="form-select" id="follow_up_required" name="follow_up_required">
                                    <option value="No" <?= old('follow_up_required') === 'No' ? 'selected' : '' ?>>No</option>
                                    <option value="Yes" <?= old('follow_up_required') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="follow_up_date" 
                                       name="follow_up_date" 
                                       value="<?= e(old('follow_up_date')) ?>"
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Consultation
                        </button>
                        <a href="<?= url('consultations') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Fill in all relevant medical information
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Include detailed chief complaint
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Document physical examination findings
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Provide clear diagnosis
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Set follow-up if required
                    </li>
                </ul>
            </div>
        </div>
        
        <?php if ($patient): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('patients/show/' . $patient['id']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-person me-2"></i>View Patient Profile
                    </a>
                    <?php if ($appointment): ?>
                    <a href="<?= url('appointments/show/' . $appointment['id']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-calendar me-2"></i>View Appointment
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>