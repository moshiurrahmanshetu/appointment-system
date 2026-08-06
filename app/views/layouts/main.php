
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= e($title ?? config('name')) ?></title>
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
    

    

</head>
<body class="dashboard-body">
    <?php if (auth()): ?>
        <?php require __DIR__ . '/../partials/navbar.php'; ?>
        
        <div class="dashboard-wrapper">
            <!-- Mobile Sidebar Backdrop -->
            <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
            
            <!-- Sidebar -->
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="main-content">
                <div class="content-wrapper">
                    <?= $content ?>
                </div>
                
                <!-- Footer -->
                <?php require __DIR__ . '/../partials/footer.php'; ?>
            </main>
        </div>
    <?php else: ?>
        <div class="auth-wrapper">
            <?= $content ?>
        </div>
    <?php endif; ?>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/dashboard.js') ?>"></script>
</body>
</html>