<?php \App\Core\View::layout(null); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - <?= config('name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="text-center">
        <div class="display-1 text-danger mb-4">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1 class="display-4 fw-bold text-danger">403</h1>
        <h2 class="mb-4">Forbidden</h2>
        <p class="lead text-muted mb-4">
            You don't have permission to access this page.
        </p>
        <p class="text-muted mb-4">
            Please contact the administrator if you believe this is an error.
        </p>
        <a href="<?= url() ?>" class="btn btn-primary">
            <i class="bi bi-house me-2"></i>Go to Homepage
        </a>
    </div>
</body>
</html>
