<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= e($title ?? config('name')) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <?php if (auth()): ?>
        <?php require __DIR__ . '/../partials/navbar.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <?php require __DIR__ . '/../partials/sidebar.php'; ?>
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                    <?= $content ?>
                </main>
            </div>
        </div>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    <?php else: ?>
        <div class="auth-wrapper">
            <?= $content ?>
        </div>
    <?php endif; ?>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
