<?php if (has_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e(flash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e(flash('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Profile</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= url('profile') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Profile
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?= url('profile/edit') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User ID</label>
                        <input type="text" class="form-control" id="user_id" value="<?= e($user['user_id']) ?>" disabled>
                        <small class="text-muted">User ID cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
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
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= e(old('email') ?: $user['email']) ?>">
                        <?php if (has_error('email')): ?>
                            <div class="invalid-feedback"><?= e(error('email')) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= e(old('phone') ?: $user['phone']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" 
                                  id="address" 
                                  name="address" 
                                  rows="3"><?= e(old('address') ?: $user['address']) ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Save Changes
                        </button>
                        <a href="<?= url('profile') ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
