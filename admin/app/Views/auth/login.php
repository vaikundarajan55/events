<?php $site = config('Site'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login · <?= esc($site->siteName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="brand-mark d-grid"><i class="bi bi-compass"></i></div>
        <h1>Welcome back</h1>
        <p class="text-center text-muted mb-4">Log in to manage <?= esc($site->siteName) ?>.</p>

        <?php if ($m = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i><?= esc($m) ?></div>
        <?php endif; ?>
        <?php if ($m = session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i><?= esc($m) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('login') ?>" data-loading novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <div class="input-icon">
                    <i class="bi bi-envelope lead-i"></i>
                    <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email', null, false)) ?>"
                           placeholder="admin@example.com" autocomplete="username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon">
                    <i class="bi bi-lock lead-i"></i>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Your password" autocomplete="current-password" required>
                    <button type="button" class="toggle-pass" data-target="#password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Log in</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>
