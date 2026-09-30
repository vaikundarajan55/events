<?php
/** @var string $pageTitle */
require_once __DIR__ . '/functions.php';
$current = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$links = ['index.php' => 'Home', 'gallery.php' => 'Gallery', 'career.php' => 'Careers'];
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($pageTitle ?? 'Home') . ' · ' . SITE_NAME) ?></title>
    <meta name="description" content="<?= e($pageDesc ?? SITE_TAGLINE) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md site-nav fixed-top" id="siteNav">
    <div class="container">
        <a class="navbar-brand" href="index.php"><span class="mark"><i class="bi bi-compass"></i></span><?= e(SITE_NAME) ?></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-md-center gap-md-1">
                <?php foreach ($links as $file => $label): ?>
                    <li class="nav-item"><a class="nav-link <?= $current === $file ? 'active' : '' ?>" href="<?= $file ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
                <li class="nav-item ms-md-2"><a class="btn btn-sm btn-outline-ink" href="<?= e(ADMIN_URL) ?>"><i class="bi bi-lock me-1"></i>Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<?php if ($flash): ?>
    <div class="container flash-wrap">
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i><?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
