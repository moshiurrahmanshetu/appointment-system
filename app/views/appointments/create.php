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
            <li class="breadcrumb-item active" aria-current="page">New Appointment</li>
        </ol>
    </nav>
    <h1 class="page-title">New Appointment</h1>
    <p class="text-muted">Schedule a new patient appointment</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Appointment Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('appointments') ?>" id="createAppointmentForm">
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
                                <option value="<?= $patient['id'] ?>" <?= old('patient_id') == $patient['id'] ? 'selected' : '' ?>>
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
                                <option value="<?= $doctor['id'] ?>" <?= old('doctor_id') == $doctor['id'] ? 'selected' : '' ?>>
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
                                   value="<?= e(old('appointment_date')) ?>" 
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
                                   value="<?= e(old('appointment_time')) ?>" 
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
                                <option value="New" <?= old('visit_type') === 'New' ? 'selected' : '' ?>>New Visit</option>
                                <option value="Follow-up" <?= old('visit_type') === 'Follow-up' ? 'selected' : '' ?>>Follow-up</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="Normal" <?= old('priority') === 'Normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="Urgent" <?= old('priority') === 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                                <option value="Emergency" <?= old('priority') === 'Emergency' ? 'selected' : '' ?>>Emergency</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= e(old('remarks')) ?></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Appointment number and serial number will be generated automatically.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Create Appointment
                        </button>
                        <a href="<?= url('appointments') ?>" class="btn btn-outline-secondary">
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
                <h5 class="card-title mb-0">Appointment Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Select patient from registered patients
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Choose available doctor and time slot
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Serial number assigned per doctor per day
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Priority helps in queue management
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        System generates appointment number
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Priority Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-secondary">Normal</span>
                    <p class="small text-muted mb-0">Regular appointments</p>
                </div>
                <div class="mb-3">
                    <span class="badge bg-warning">Urgent</span>
                    <p class="small text-muted mb-0">High priority, seen earlier</p>
                </div>
                <div>
                    <span class="badge bg-danger">Emergency</span>
                    <p class="small text-muted mb-0">Immediate attention required</p>
                </div>
            </div>
        </div>
    </div>
</div>