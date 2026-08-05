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
    <h1 class="h2">Change Password</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= url('profile') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Profile
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    You must enter your current password to change it.
                </div>
                
                <form method="POST" action="<?= url('profile/change-password') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" 
                                   class="form-control <?= has_error('current_password') ? 'is-invalid' : '' ?>" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            <?php if (has_error('current_password')): ?>
                                <div class="invalid-feedback"><?= e(error('current_password')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" 
                                   class="form-control <?= has_error('new_password') ? 'is-invalid' : '' ?>" 
                                   id="new_password" 
                                   name="new_password" 
                                   required
                                   minlength="6">
                            <?php if (has_error('new_password')): ?>
                                <div class="invalid-feedback"><?= e(error('new_password')) ?></div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Password must be at least 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" 
                                   class="form-control <?= has_error('new_password_confirmation') ? 'is-invalid' : '' ?>" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   required
                                   minlength="6">
                            <?php if (has_error('new_password_confirmation')): ?>
                                <div class="invalid-feedback"><?= e(error('new_password_confirmation')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Change Password
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
