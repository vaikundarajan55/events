<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <form class="d-flex flex-wrap gap-2" method="get" action="<?= site_url('gallery') ?>">
        <input type="search" name="q" value="<?= esc($q) ?>" class="form-control" style="max-width:240px" placeholder="Search title…">
        <select name="status" class="form-select" style="max-width:150px" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Visible</option>
            <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Hidden</option>
        </select>
        <button class="btn btn-light border"><i class="bi bi-search"></i></button>
    </form>
    <a href="<?= site_url('gallery/create') ?>" class="btn btn-primary"><i class="bi bi-cloud-arrow-up me-1"></i> Add images</a>
</div>

<?php if (! $items): ?>
    <div class="card"><div class="empty"><i class="bi bi-images"></i>
        <p class="mt-2 mb-3">No images found.</p>
        <a href="<?= site_url('gallery/create') ?>" class="btn btn-primary btn-sm">Upload your first images</a>
    </div></div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $i => $g):
            $src = $siteUrl . 'uploads/gallery/' . (! empty($g['thumb']) ? 'thumbs/' . $g['thumb'] : $g['image']); ?>
            <div class="col-6 col-md-4 col-xl-3 reveal" style="--i:<?= $i ?>">
                <div class="card g-card h-100">
                    <div class="g-thumb">
                        <img src="<?= esc($src) ?>" alt="<?= esc($g['title']) ?>" loading="lazy">
                        <span class="badge-soft <?= $g['status'] ? 'st-on' : 'st-off' ?>"><?= $g['status'] ? 'Visible' : 'Hidden' ?></span>
                    </div>
                    <div class="card-body p-3">
                        <div class="fw-semibold text-truncate" title="<?= esc($g['title']) ?>"><?= esc($g['title']) ?></div>
                        <div class="small text-muted mb-2"><?= date('d M Y', strtotime($g['created_at'])) ?></div>
                        <div class="d-flex gap-1">
                            <a class="btn btn-sm btn-light border flex-fill" href="<?= site_url('gallery/edit/' . $g['id']) ?>" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('gallery/toggle/' . $g['id']) ?>" class="flex-fill">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light border w-100" title="<?= $g['status'] ? 'Hide' : 'Show' ?>"><i class="bi <?= $g['status'] ? 'bi-eye-slash' : 'bi-eye' ?>"></i></button>
                            </form>
                            <button class="btn btn-sm btn-light border text-danger flex-fill" title="Delete"
                                    data-delete-url="<?= site_url('gallery/delete/' . $g['id']) ?>"
                                    data-delete-name="“<?= esc($g['title']) ?>”"><i class="bi bi-trash3"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4">
        <div class="small text-muted">Showing <?= $from ?>–<?= $to ?> of <?= $total ?></div>
        <?= $pager->links('default', 'bootstrap_full') ?>
        <div class="d-none d-md-block" style="width:120px"></div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
