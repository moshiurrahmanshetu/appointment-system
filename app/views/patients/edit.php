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
            <li class="breadcrumb-item active" aria-current="page">Edit Patient</li>
        </ol>
    </nav>
    <h1 class="page-title">Edit Patient</h1>
    <p class="text-muted">Update patient information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Patient Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('patients/update/' . $patient['id']) ?>" enctype="multipart/form-data" id="editPatientForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= has_error('full_name') ? 'is-invalid' : '' ?>" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= e(old('full_name') ?: $patient['full_name']) ?>" 
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
                                   value="<?= e(old('phone') ?: $patient['phone']) ?>" 
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
                                <option value="male" <?= (old('gender') ?: $patient['gender']) === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= (old('gender') ?: $patient['gender']) === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= (old('gender') ?: $patient['gender']) === 'other' ? 'selected' : '' ?>>Other</option>
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
                                   value="<?= e(old('dob') ?: $patient['dob']) ?>" 
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
                                <option value="A+" <?= (old('blood_group') ?: $patient['blood_group']) === 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= (old('blood_group') ?: $patient['blood_group']) === 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= (old('blood_group') ?: $patient['blood_group']) === 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= (old('blood_group') ?: $patient['blood_group']) === 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= (old('blood_group') ?: $patient['blood_group']) === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= (old('blood_group') ?: $patient['blood_group']) === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= (old('blood_group') ?: $patient['blood_group']) === 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= (old('blood_group') ?: $patient['blood_group']) === 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= (old('status') ?: $patient['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (old('status') ?: $patient['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="blocked" <?= (old('status') ?: $patient['status']) === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= e(old('address') ?: $patient['address']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact" class="form-label">Emergency Contact</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="emergency_contact" 
                                   name="emergency_contact" 
                                   value="<?= e(old('emergency_contact') ?: $patient['emergency_contact']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="emergency_phone" class="form-label">Emergency Phone</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="emergency_phone" 
                                   name="emergency_phone" 
                                   value="<?= e(old('emergency_phone') ?: $patient['emergency_phone']) ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Patient Photo</label>
                        <input type="file" 
                               class="form-control" 
                               id="photo" 
                               name="photo" 
                               accept="image/jpeg,image/jpg,image/png">
                        <div class="form-text">Allowed formats: JPG, JPEG, PNG. Maximum size: 2MB.</div>
                        
                        <?php if ($patient['photo']): ?>
                        <div class="mt-2">
                            <label class="small text-muted">Current Photo:</label>
                            <div class="d-flex align-items-center mt-1">
                                <img src="<?= asset($patient['photo']) ?>" alt="Current Photo" class="rounded-circle me-2" width="50" height="50" style="object-fit: cover;">
                                <span class="small text-muted">Leave blank to keep current photo</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Patient
                        </button>
                        <a href="<?= url('patients/show/' . $patient['id']) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Read-only Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Read-only Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Patient Code</label>
                    <div class="fw-bold"><code><?= e($patient['patient_code']) ?></code></div>
                </div>
                
                <?php if ($patient['user_id']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Linked User ID</label>
                    <div class="fw-bold">
                        <?php
                        $linkedUser = $patient['login_id'] ?? null;
                        echo $linkedUser ? e($linkedUser) : 'N/A';
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Created Date</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($patient['created_at'])) ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Last Updated</label>
                    <div class="fw-bold"><?= date('F j, Y g:i A', strtotime($patient['updated_at'])) ?></div>
                </div>
            </div>
        </div>
        
        <!-- Account Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Status</h5>
            </div>
            <div class="card-body">
                <?php if ($patient['user_id']): ?>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Patient Status</label>
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
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Login Account Status</label>
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
                
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-2"></i>
                    Changing patient status will also update the linked login account status.
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
</div>
