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
            <li class="breadcrumb-item"><a href="<?= url('users') ?>">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create User</li>
        </ol>
    </nav>
    <h1 class="page-title">Create User</h1>
    <p class="text-muted">Add a new user to the system</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('users') ?>" enctype="multipart/form-data" id="createUserForm">
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
                            <label for="username" class="form-label">Username (Optional)</label>
                            <input type="text" 
                                   class="form-control <?= has_error('username') ? 'is-invalid' : '' ?>" 
                                   id="username" 
                                   name="username" 
                                   value="<?= e(old('username')) ?>">
                            <?php if (has_error('username')): ?>
                            <div class="invalid-feedback"><?= e(error('username')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email (Optional)</label>
                            <input type="email" 
                                   class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= e(old('email')) ?>">
                            <?php if (has_error('email')): ?>
                            <div class="invalid-feedback"><?= e(error('email')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" 
                                   class="form-control <?= has_error('phone') ? 'is-invalid' : '' ?>" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= e(old('phone')) ?>">
                            <?php if (has_error('phone')): ?>
                            <div class="invalid-feedback"><?= e(error('phone')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= e(old('address')) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select <?= has_error('role_id') ? 'is-invalid' : '' ?>" 
                                    id="role_id" 
                                    name="role_id" 
                                    required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                    <?= e($role['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('role_id')): ?>
                            <div class="invalid-feedback"><?= e(error('role_id')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-toggle-password="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if (has_error('password')): ?>
                                <div class="invalid-feedback"><?= e(error('password')) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control <?= has_error('password_confirmation') ? 'is-invalid' : '' ?>" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                            <?php if (has_error('password_confirmation')): ?>
                            <div class="invalid-feedback"><?= e(error('password_confirmation')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="blocked" <?= old('status') === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="avatar" 
                                   name="avatar" 
                                   accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">JPG, JPEG, PNG only. Maximum 2MB.</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="generate_user_id" checked disabled>
                            <label class="form-check-label" for="generate_user_id">
                                Auto-generate User ID
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create User
                        </button>
                        <a href="<?= url('users') ?>" class="btn btn-outline-secondary">
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
                <h5 class="card-title mb-0">User ID Preview</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <div class="user-id-preview bg-light rounded p-3 mb-3">
                        <code class="fs-4">USRXXXXXX</code>
                    </div>
                    <p class="text-muted small mb-0">
                        User ID will be automatically generated upon creation.
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
                        <strong>User ID:</strong> Auto-generated unique identifier
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Username:</strong> Optional field for display purposes
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Phone:</strong> Required for contact
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Role:</strong> Determines user permissions
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Status:</strong> Active users can login
                    </li>
                    <li>
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Avatar:</strong> Optional profile image
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-toggle-password');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            }
        });
    });
    
    // Form submission with loading state
    const form = document.getElementById('createUserForm');
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
