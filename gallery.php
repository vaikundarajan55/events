<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo   = db();
$q     = trim((string) ($_GET['q'] ?? ''));
$page  = current_page_number();

$where  = 'WHERE status = 1';
$params = [];
if ($q !== '') {
    $where .= ' AND title LIKE :q';
    $params[':q'] = '%' . $q . '%';
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM gallery $where");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();
$pages = max(1, (int) ceil($total / GALLERY_PER_PAGE));
$page  = min($page, $pages);
$offset = ($page - 1) * GALLERY_PER_PAGE;

$stmt = $pdo->prepare("SELECT * FROM gallery $where ORDER BY id DESC LIMIT " . GALLERY_PER_PAGE . " OFFSET $offset");
$stmt->execute($params);
$items = $stmt->fetchAll();

$pageTitle = 'Gallery';
$pageDesc  = 'Photos from around the studio.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <h1 class="page-title">Gallery</h1>
        <p class="text-muted mb-0">Workshops, offsites and everyday moments.</p>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <form class="row g-2 mb-4 justify-content-between" method="get" action="gallery.php">
            <div class="col-md-5 col-lg-4">
                <div class="input-group">
                    <input type="search" name="q" class="form-control" placeholder="Search photos…" value="<?= e($q) ?>">
                    <button class="btn btn-ink" aria-label="Search"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="col-md-auto small text-muted align-self-center">
                <?= $total ?> photo<?= $total === 1 ? '' : 's' ?><?= $q !== '' ? ' matching “' . e($q) . '”' : '' ?>
            </div>
        </form>

        <?php if (!$items): ?>
            <div class="empty-state"><i class="bi bi-images"></i><p class="mb-0">No photos found.</p></div>
        <?php else: ?>
            <div class="row g-3" id="galleryGrid">
                <?php foreach ($items as $i => $g): ?>
                    <div class="col-6 col-md-4" data-aos="zoom-in" data-aos-delay="<?= ($i % 3) * 80 ?>">
                        <a href="<?= gallery_full_url($g) ?>" class="tile g-item" data-title="<?= e($g['title']) ?>">
                            <img src="<?= gallery_thumb_url($g) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                            <span class="tile-cap"><?= e($g['title']) ?></span>
                            <span class="tile-zoom"><i class="bi bi-arrows-fullscreen"></i></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-5"><?= render_pagination('gallery.php', ['q' => $q], $page, $pages) ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox -->
<div class="modal fade" id="lightbox" tabindex="-1" aria-label="Image viewer" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content lightbox">
            <button type="button" class="btn-close btn-close-white lb-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <button type="button" class="lb-nav lb-prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
            <img id="lbImg" src="" alt="">
            <button type="button" class="lb-nav lb-next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            <div class="lb-caption" id="lbCaption"></div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
