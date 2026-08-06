<?php \App\Core\View::layout(null); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - <?= config('name') ?></title>
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
</head>

<body class="login-page">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="bi bi-hospital"></i>
                </div>
                <h1><?= config('name') ?></h1>
                <p class="text-muted">Sign in to your account</p>
            </div>
            
            <?php if (has_flash('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= e(flash('error')) ?>
            </div>
            <?php endif; ?>
            
            <?php if (has_flash('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= e(flash('success')) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= url('login') ?>">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="user_id" class="form-label">User ID</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" 
                               class="form-control <?= has_error('user_id') ? 'is-invalid' : '' ?>" 
                               id="user_id" 
                               name="user_id" 
                               placeholder="Enter your User ID"
                               value="<?= e(old('user_id')) ?>"
                               required
                               autofocus>
                    </div>
                    <?php if (has_error('user_id')): ?>
                    <div class="invalid-feedback"><?= e(error('user_id')) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required>
                    </div>
                    <?php if (has_error('password')): ?>
                    <div class="invalid-feedback"><?= e(error('password')) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="remember" 
                           name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>
            
            <div class="auth-footer">
                <p class="text-muted small">
                    © <?= date('Y') ?> <?= config('name') ?>. All rights reserved.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>