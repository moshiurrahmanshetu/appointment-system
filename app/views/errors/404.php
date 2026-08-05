<?php \App\Core\View::layout(null); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - <?= config('name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="text-center">
        <div class="display-1 text-warning mb-4">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h1 class="display-4 fw-bold text-warning">404</h1>
        <h2 class="mb-4">Page Not Found</h2>
        <p class="lead text-muted mb-4">
            The page you are looking for doesn't exist or has been moved.
        </p>
        <p class="text-muted mb-4">
            Please check the URL or go back to the homepage.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="<?= url() ?>" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>Go to Homepage
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Go Back
            </a>
        </div>
    </div>
</body>
</html>
