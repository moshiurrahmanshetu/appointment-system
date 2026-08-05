<?php \App\Core\View::layout(null); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - <?= config('name') ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body class="login-page">
    <div class="login-container">
        <!-- Left Section - Branding -->
        <div class="login-branding d-none d-lg-flex flex-column justify-content-center">
            <div class="branding-content text-center text-white">
                <div class="branding-icon mb-4">
                    <i class="bi bi-hospital"></i>
                </div>
                <h2 class="branding-title"><?= config('name') ?></h2>
                <p class="branding-subtitle">Professional Appointment Queue Management</p>
            </div>
        </div>
        
        <!-- Right Section - Login Form -->
        <div class="login-form-wrapper">
            <div class="login-form-container">
                <div class="text-center mb-5">
                    <div class="mobile-logo d-lg-none mb-3">
                        <i class="bi bi-hospital text-primary"></i>
                    </div>
                    <h3 class="login-title">Welcome Back</h3>
                    <p class="login-subtitle">Sign in to your account to continue</p>
                </div>
                
                <?php if (has_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= e(flash('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (has_flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= e(flash('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= url('login') ?>" id="loginForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label for="user_id" class="form-label">User ID</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" 
                                   class="form-control <?= has_error('user_id') ? 'is-invalid' : '' ?>" 
                                   id="user_id" 
                                   name="user_id" 
                                   value="<?= e(old('user_id')) ?>" 
                                   placeholder="Enter your user ID"
                                   required
                                   autofocus>
                            <?php if (has_error('user_id')): ?>
                                <div class="invalid-feedback"><?= e(error('user_id')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-toggle-password="password">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if (has_error('password')): ?>
                                <div class="invalid-feedback"><?= e(error('password')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg login-btn" id="loginBtn">
                            <span class="btn-text">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Signing in...
                            </span>
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Default Admin: <strong>admin</strong> / <strong>admin123</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/auth.js"></script>
</body>
</html>
