<?php \App\Core\View::layout(null); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error - <?= config('name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="text-center">
        <div class="display-1 text-danger mb-4">
            <i class="bi bi-bug"></i>
        </div>
        <h1 class="display-4 fw-bold text-danger">500</h1>
        <h2 class="mb-4">Server Error</h2>
        <p class="lead text-muted mb-4">
            Something went wrong on our end. Please try again later.
        </p>
        <p class="text-muted mb-4">
            Our team has been notified and is working to fix the issue.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="<?= url() ?>" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>Go to Homepage
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
            </a>
        </div>
    </div>
</body>
</html>
