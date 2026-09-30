<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

$latest = $pdo->query('SELECT * FROM gallery WHERE status = 1 ORDER BY id DESC LIMIT 6')->fetchAll();
$jobs   = $pdo->query('SELECT id, title, department, location, job_type FROM careers WHERE status = 1 ORDER BY id DESC LIMIT 3')->fetchAll();
$stats  = [
    'roles'  => (int) $pdo->query('SELECT COUNT(*) FROM careers WHERE status = 1')->fetchColumn(),
    'photos' => (int) $pdo->query('SELECT COUNT(*) FROM gallery WHERE status = 1')->fetchColumn(),
];

$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>
<header class="hero">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <h1 class="hero-title">We build web products people actually enjoy using.</h1>
                <p class="lead text-muted my-4">
                    <?= e(SITE_NAME) ?> is a small team of developers and designers. We work in the open,
                    ship in small steps, and hire people who like doing the same.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="career.php" class="btn btn-ink btn-lg">See open roles<?php if ($stats['roles']): ?> (<?= $stats['roles'] ?>)<?php endif; ?></a>
                    <a href="gallery.php" class="btn btn-outline-ink btn-lg">Life at <?= e(SITE_NAME) ?></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="collage">
                    <?php foreach (array_slice($latest, 0, 3) as $i => $g): ?>
                        <figure class="collage-item c<?= $i + 1 ?>">
                            <img src="<?= gallery_thumb_url($g) ?>" alt="<?= e($g['title']) ?>">
                            <figcaption><?= e($g['title']) ?></figcaption>
                        </figure>
                    <?php endforeach; ?>
                    <?php if (!$latest): ?>
                        <div class="text-muted">Photos will appear here once they are added in the admin panel.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
            <div>
                <h2 class="section-title mb-1">Recently around the studio</h2>
                <p class="text-muted mb-0"><?= $stats['photos'] ?> photos in the gallery.</p>
            </div>
            <a href="gallery.php" class="btn btn-outline-ink d-none d-sm-inline-block">Open gallery</a>
        </div>
        <div class="row g-3">
            <?php foreach ($latest as $i => $g): ?>
                <div class="col-6 col-md-4" data-aos="zoom-in" data-aos-delay="<?= $i * 70 ?>">
                    <a class="tile" href="gallery.php">
                        <img src="<?= gallery_thumb_url($g) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                        <span class="tile-cap"><?= e($g['title']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
            <div>
                <h2 class="section-title mb-1">Open positions</h2>
                <p class="text-muted mb-0">Join a team that reviews every application personally.</p>
            </div>
            <a href="career.php" class="btn btn-outline-ink d-none d-sm-inline-block">All jobs</a>
        </div>

        <?php if (!$jobs): ?>
            <p class="text-muted">There are no open roles right now. Check back soon.</p>
        <?php endif; ?>
        <div class="row g-3">
            <?php foreach ($jobs as $i => $j): ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $i * 90 ?>">
                    <a href="career.php" class="job-mini">
                        <span class="chip"><?= e($j['job_type']) ?></span>
                        <h3><?= e($j['title']) ?></h3>
                        <div class="text-muted small"><i class="bi bi-diagram-3 me-1"></i><?= e($j['department']) ?> &nbsp; <i class="bi bi-geo-alt me-1"></i><?= e($j['location']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cta" data-aos="fade-up">
            <h2>Don't see your role?</h2>
            <p class="mb-4">Send us a note about what you'd like to build. We read everything.</p>
            <a class="btn btn-amber btn-lg" href="mailto:<?= e(CONTACT_EMAIL) ?>">Email the team</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
