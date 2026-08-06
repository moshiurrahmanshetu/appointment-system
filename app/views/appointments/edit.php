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
            <li class="breadcrumb-item active" aria-current="page">Edit Appointment</li>
        </ol>
    </nav>
    <h1 class="page-title">Edit Appointment</h1>
    <p class="text-muted">Update appointment details</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Appointment Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('appointments/update/' . $appointment['id']) ?>" id="editAppointmentForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                            <select class="form-select <?= has_error('patient_id') ? 'is-invalid' : '' ?>" 
                                    id="patient_id" 
                                    name="patient_id" 
                                    required>
                                <option value="">Select Patient</option>
                                <?php foreach ($patients as $patient): ?>
                                <option value="<?= $patient['id'] ?>" <?= old('patient_id') ?: $appointment['patient_id'] == $patient['id'] ? 'selected' : '' ?>>
                                    <?= e($patient['full_name']) ?> (<?= e($patient['patient_code']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('patient_id')): ?>
                            <div class="invalid-feedback"><?= e(error('patient_id')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="doctor_id" class="form-label">Doctor <span class="text-danger">*</span></label>
                            <select class="form-select <?= has_error('doctor_id') ? 'is-invalid' : '' ?>" 
                                    id="doctor_id" 
                                    name="doctor_id" 
                                    required>
                                <option value="">Select Doctor</option>
                                <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>" <?= old('doctor_id') ?: $appointment['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                    <?= e($doctor['full_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('doctor_id')): ?>
                            <div class="invalid-feedback"><?= e(error('doctor_id')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control <?= has_error('appointment_date') ? 'is-invalid' : '' ?>" 
                                   id="appointment_date" 
                                   name="appointment_date" 
                                   value="<?= e(old('appointment_date') ?: $appointment['appointment_date']) ?>" 
                                   required
                                   min="<?= date('Y-m-d') ?>">
                            <?php if (has_error('appointment_date')): ?>
                            <div class="invalid-feedback"><?= e(error('appointment_date')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="appointment_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                            <input type="time" 
                                   class="form-control <?= has_error('appointment_time') ? 'is-invalid' : '' ?>" 
                                   id="appointment_time" 
                                   name="appointment_time" 
                                   value="<?= e(old('appointment_time') ?: $appointment['appointment_time']) ?>" 
                                   required>
                            <?php if (has_error('appointment_time')): ?>
                            <div class="invalid-feedback"><?= e(error('appointment_time')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="visit_type" class="form-label">Visit Type</label>
                            <select class="form-select" id="visit_type" name="visit_type">
                                <option value="New" <?= (old('visit_type') ?: $appointment['visit_type']) === 'New' ? 'selected' : '' ?>>New Visit</option>
                                <option value="Follow-up" <?= (old('visit_type') ?: $appointment['visit_type']) === 'Follow-up' ? 'selected' : '' ?>>Follow-up</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="Normal" <?= (old('priority') ?: $appointment['priority']) === 'Normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="Urgent" <?= (old('priority') ?: $appointment['priority']) === 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                                <option value="Emergency" <?= (old('priority') ?: $appointment['priority']) === 'Emergency' ? 'selected' : '' ?>>Emergency</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Pending" <?= (old('status') ?: $appointment['status']) === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Confirmed" <?= (old('status') ?: $appointment['status']) === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="Checked In" <?= (old('status') ?: $appointment['status']) === 'Checked In' ? 'selected' : '' ?>>Checked In</option>
                                <option value="In Queue" <?= (old('status') ?: $appointment['status']) === 'In Queue' ? 'selected' : '' ?>>In Queue</option>
                                <option value="With Doctor" <?= (old('status') ?: $appointment['status']) === 'With Doctor' ? 'selected' : '' ?>>With Doctor</option>
                                <option value="Completed" <?= (old('status') ?: $appointment['status']) === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= (old('status') ?: $appointment['status']) === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial No</label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-primary">#<?= $appointment['serial_no'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= e(old('remarks') ?: $appointment['remarks']) ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Appointment
                        </button>
                        <a href="<?= url('appointments/show/' . $appointment['id']) ?>" class="btn btn-outline-secondary">
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
                <h5 class="card-title mb-0">Read-only Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Appointment No</label>
                    <div class="fw-bold"><code><?= e($appointment['appointment_no']) ?></code></div>
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
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Status</h5>
            </div>
            <div class="card-body">
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
                <span class="badge <?= $statusClass ?> fs-5"><?= e($appointment['status']) ?></span>
            </div>
        </div>
    </div>
</div>