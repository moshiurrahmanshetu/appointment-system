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
            <li class="breadcrumb-item active" aria-current="page">Add Patient</li>
        </ol>
    </nav>
    <h1 class="page-title">Add Patient</h1>
    <p class="text-muted">Register a new patient and create login account</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Patient Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('patients') ?>" enctype="multipart/form-data" id="createPatientForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= has_error('full_name') ? 'is-invalid' : '' ?>" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= e(old('full_name')) ?>" 
                                   required>
                            <?php if (has_error('full_name')): ?>
                            <div class="invalid-feedback"><?= e(error('full_name')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   class="form-control <?= has_error('phone') ? 'is-invalid' : '' ?>" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= e(old('phone')) ?>" 
                                   required>
                            <?php if (has_error('phone')): ?>
                            <div class="invalid-feedback"><?= e(error('phone')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select <?= has_error('gender') ? 'is-invalid' : '' ?>" 
                                    id="gender" 
                                    name="gender" 
                                    required>
                                <option value="">Select Gender</option>
                                <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if (has_error('gender')): ?>
                            <div class="invalid-feedback"><?= e(error('gender')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control <?= has_error('dob') ? 'is-invalid' : '' ?>" 
                                   id="dob" 
                                   name="dob" 
                                   value="<?= e(old('dob')) ?>" 
                                   required>
                            <?php if (has_error('dob')): ?>
                            <div class="invalid-feedback"><?= e(error('dob')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label">Blood Group</label>
                            <select class="form-select" id="blood_group" name="blood_group">
                                <option value="">Select Blood Group</option>
                                <option value="A+" <?= old('blood_group') === 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= old('blood_group') === 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= old('blood_group') === 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= old('blood_group') === 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= old('blood_group') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= old('blood_group') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= old('blood_group') === 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= old('blood_group') === 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="blocked" <?= old('status') === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= e(old('address')) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact" class="form-label">Emergency Contact</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="emergency_contact" 
                                   name="emergency_contact" 
                                   value="<?= e(old('emergency_contact')) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="emergency_phone" class="form-label">Emergency Phone</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="emergency_phone" 
                                   name="emergency_phone" 
                                   value="<?= e(old('emergency_phone')) ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Patient Photo</label>
                        <input type="file" 
                               class="form-control" 
                               id="photo" 
                               name="photo" 
                               accept="image/jpeg,image/jpg,image/png">
                        <div class="form-text">JPG, JPEG, PNG only. Maximum 2MB.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Login Account:</strong> A login account will be automatically created for this patient. The patient's phone number will be used as the default password.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create Patient
                        </button>
                        <a href="<?= url('patients') ?>" class="btn btn-outline-secondary">
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
                <h5 class="card-title mb-0">Patient Code Preview</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <div class="patient-code-preview bg-light rounded p-3 mb-3">
                        <code class="fs-4">PAT-000001</code>
                    </div>
                    <p class="text-muted small mb-0">
                        Patient code will be automatically generated upon creation.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Help</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Patient Code:</strong> Auto-generated unique identifier
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Phone:</strong> Required for contact and default password
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>DOB:</strong> Patient's date of birth
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Blood Group:</strong> Medical information
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Emergency Contact:</strong> Emergency contact person
                    </li>
                    <li>
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Login Account:</strong> Auto-created with phone as password
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission with loading state
    const form = document.getElementById('createPatientForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            }
        });
    }
});
</script>
