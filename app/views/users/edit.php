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
            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
        </ol>
    </nav>
    <h1 class="page-title">Edit User</h1>
    <p class="text-muted">Update user information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('users/update/' . $user['id']) ?>" enctype="multipart/form-data" id="editUserForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= has_error('full_name') ? 'is-invalid' : '' ?>" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= e(old('full_name') ?: $user['full_name']) ?>" 
                                   required>
                            <?php if (has_error('full_name')): ?>
                            <div class="invalid-feedback"><?= e(error('full_name')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">User ID</label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   id="user_id" 
                                   name="user_id" 
                                   value="<?= e($user['user_id']) ?>" 
                                   readonly>
                            <div class="form-text">User ID cannot be changed</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username (Optional)</label>
                            <input type="text" 
                                   class="form-control <?= has_error('username') ? 'is-invalid' : '' ?>" 
                                   id="username" 
                                   name="username" 
                                   value="<?= e(old('username') ?: $user['username']) ?>">
                            <?php if (has_error('username')): ?>
                            <div class="invalid-feedback"><?= e(error('username')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email (Optional)</label>
                            <input type="email" 
                                   class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= e(old('email') ?: $user['email']) ?>">
                            <?php if (has_error('email')): ?>
                            <div class="invalid-feedback"><?= e(error('email')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" 
                                   class="form-control <?= has_error('phone') ? 'is-invalid' : '' ?>" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= e(old('phone') ?: $user['phone']) ?>">
                            <?php if (has_error('phone')): ?>
                            <div class="invalid-feedback"><?= e(error('phone')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= (old('gender') ?: $user['gender']) === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= (old('gender') ?: $user['gender']) === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= (old('gender') ?: $user['gender']) === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= e(old('address') ?: $user['address']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select <?= has_error('role_id') ? 'is-invalid' : '' ?>" 
                                    id="role_id" 
                                    name="role_id" 
                                    required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= (old('role_id') ?: $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                    <?= e($role['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('role_id')): ?>
                            <div class="invalid-feedback"><?= e(error('role_id')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= (old('status') ?: $user['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (old('status') ?: $user['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="blocked" <?= (old('status') ?: $user['status']) === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <input type="file" 
                               class="form-control" 
                               id="avatar" 
                               name="avatar" 
                               accept="image/jpeg,image/jpg,image/png">
                        <div class="form-text">JPG, JPEG, PNG only. Maximum 2MB. Leave empty to keep current avatar.</div>
                    </div>
                    
                    <?php if ($user['avatar']): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Avatar</label>
                        <div class="d-flex align-items-center">
                            <img src="<?= asset($user['avatar']) ?>" alt="Current Avatar" class="rounded-circle me-3" width="60" height="60">
                            <small class="text-muted">Current avatar will be replaced if you upload a new one</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update User
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
                <h5 class="card-title mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>User ID:</th>
                        <td><code><?= e($user['user_id']) ?></code></td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td><?= e($user['role_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $statusClass = match($user['status']) {
                                'active' => 'bg-success',
                                'inactive' => 'bg-warning',
                                'blocked' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($user['status']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td><?= date('M d, Y', strtotime($user['updated_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('users/' . $user['id']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>View Profile
                    </a>
                    <?php if (can('users.delete')): ?>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                        <i class="bi bi-trash me-2"></i>Delete User
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = '<?= url('users/delete') ?>/' + userId;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Form submission with loading state
    const form = document.getElementById('editUserForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            }
        });
    }
});
</script>
