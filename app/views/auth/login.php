<?php \App\Core\View::layout(null); ?>

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg" style="max-width: 400px; width: 100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-calendar-check text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-3"><?= config('name') ?></h3>
                <p class="text-muted">Sign in to your account</p>
            </div>
            
            <?php if (has_flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e(flash('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (has_flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e(flash('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= url('login') ?>">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="user_id" class="form-label">User ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" 
                               class="form-control <?= has_error('user_id') ? 'is-invalid' : '' ?>" 
                               id="user_id" 
                               name="user_id" 
                               value="<?= e(old('user_id')) ?>" 
                               required
                               autofocus>
                        <?php if (has_error('user_id')): ?>
                            <div class="invalid-feedback"><?= e(error('user_id')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" 
                               class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               required>
                        <?php if (has_error('password')): ?>
                            <div class="invalid-feedback"><?= e(error('password')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted small">
                    Default Admin: admin / admin123
                </p>
            </div>
        </div>
    </div>
</div>
