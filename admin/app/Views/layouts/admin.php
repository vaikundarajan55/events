<?php
$seg   = service('uri')->getSegment(1);
$nav   = [
    'dashboard' => ['Dashboard', 'bi-speedometer2'],
    'gallery'   => ['Gallery', 'bi-images'],
    'careers'   => ['Careers', 'bi-briefcase'],
    'responses' => ['Career responses', 'bi-inbox'],
];
$site  = config('Site');
$errs  = session()->getFlashdata('errors');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin') ?> · <?= esc($site->siteName) ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= site_url('dashboard') ?>">
        <span class="brand-mark"><i class="bi bi-compass"></i></span>
        <span><?= esc($site->siteName) ?><small>Admin panel</small></span>
    </a>

    <nav class="nav flex-column">
        <?php foreach ($nav as $key => [$label, $icon]): ?>
            <a class="nav-link <?= $seg === $key ? 'active' : '' ?>" href="<?= site_url($key) ?>">
                <i class="bi <?= $icon ?>"></i> <?= $label ?>
            </a>
        <?php endforeach; ?>
        <hr>
        <a class="nav-link" href="<?= esc($site->siteUrl) ?>" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right"></i> View website
        </a>
        <a class="nav-link" href="<?= site_url('logout') ?>">
            <i class="bi bi-box-arrow-left"></i> Log out
        </a>
    </nav>
</aside>

<div class="main">
    <header class="topbar">
        <button class="btn btn-light d-lg-none" id="sidebarToggle" aria-label="Open menu"><i class="bi bi-list fs-5"></i></button>
        <h1 class="page-title mb-0"><?= esc($title ?? '') ?></h1>

        <div class="dropdown ms-auto">
            <button class="user-chip dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar"><?= esc(strtoupper(substr(session('admin_name') ?? 'A', 0, 1))) ?></span>
                <span class="d-none d-sm-inline"><?= esc(session('admin_name')) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><span class="dropdown-item-text small text-muted"><?= esc(session('admin_email')) ?></span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Log out</a></li>
            </ul>
        </div>
    </header>

    <main class="content page-enter">
        <?php if ($m = session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= esc($m) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($m = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= esc($m) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (! empty($errs) && is_array($errs)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-1"><?php foreach ($errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>

<!-- Shared delete confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" id="deleteForm">
            <?= csrf_field() ?>
            <div class="modal-body text-center p-4">
                <div class="modal-icon"><i class="bi bi-trash3"></i></div>
                <h5 class="mb-1">Delete <span id="deleteName">this item</span>?</h5>
                <p class="text-muted mb-4">This can't be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/admin.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
