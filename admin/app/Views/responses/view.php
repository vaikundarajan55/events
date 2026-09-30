<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<a href="<?= site_url('responses') ?>" class="btn btn-light border btn-sm mb-3"><i class="bi bi-arrow-left me-1"></i>All responses</a>

<div class="row g-3">
    <div class="col-lg-8 reveal">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <div>
                        <h2 class="h4 mb-1"><?= esc($a['name']) ?></h2>
                        <div class="text-muted">Applied for <strong><?= esc($a['job_name'] ?? '—') ?></strong></div>
                    </div>
                    <div><span class="badge-soft st-<?= esc($a['status']) ?> fs-6"><?= esc($a['status']) ?></span></div>
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9"><a href="mailto:<?= esc($a['email']) ?>"><?= esc($a['email']) ?></a></dd>
                    <dt class="col-sm-3">Phone</dt>
                    <dd class="col-sm-9"><a href="tel:<?= esc($a['phone']) ?>"><?= esc($a['phone']) ?></a></dd>
                    <dt class="col-sm-3">Applied on</dt>
                    <dd class="col-sm-9"><?= date('d M Y, h:i A', strtotime($a['created_at'])) ?></dd>
                    <dt class="col-sm-3">Cover note</dt>
                    <dd class="col-sm-9"><?= $a['message'] ? nl2br(esc($a['message'])) : '<span class="text-muted">—</span>' ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4 reveal" style="--i:2">
        <div class="card mb-3">
            <div class="card-header">Resume</div>
            <div class="card-body">
                <a href="<?= site_url('responses/resume/' . $a['id']) ?>" class="btn btn-primary w-100"><i class="bi bi-download me-1"></i> Download resume</a>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Update status</div>
            <div class="card-body">
                <form method="post" action="<?= site_url('responses/status/' . $a['id']) ?>" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <select name="status" class="form-select">
                        <?php foreach ($statuses as $s): ?>
                            <option <?= $a['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
        <button class="btn btn-outline-danger w-100"
                data-delete-url="<?= site_url('responses/delete/' . $a['id']) ?>"
                data-delete-name="this application"><i class="bi bi-trash3 me-1"></i> Delete application</button>
    </div>
</div>
<?= $this->endSection() ?>
