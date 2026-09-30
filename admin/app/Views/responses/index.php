<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $qs = http_build_query(array_filter(['q' => $q, 'job' => $job ?: null, 'status' => $status])); ?>
<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-center" method="get" action="<?= site_url('responses') ?>">
            <div class="col-12 col-md-4">
                <input type="search" name="q" value="<?= esc($q) ?>" class="form-control" placeholder="Search name, email or phone…">
            </div>
            <div class="col-6 col-md-3">
                <select name="job" class="form-select" onchange="this.form.submit()">
                    <option value="">All positions</option>
                    <?php foreach ($jobs as $j): ?>
                        <option value="<?= $j['id'] ?>" <?= (int) $job === (int) $j['id'] ? 'selected' : '' ?>><?= esc($j['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Any status</option>
                    <?php foreach ($statuses as $s): ?>
                        <option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2 justify-content-md-end">
                <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
                <a class="btn btn-light border" href="<?= site_url('responses') ?>">Reset</a>
                <a class="btn btn-outline-primary" href="<?= site_url('responses/export' . ($qs ? '?' . $qs : '')) ?>" title="Export CSV"><i class="bi bi-download"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>Applicant</th><th>Position</th><th>Phone</th><th>Status</th><th>Applied</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (! $items): ?>
                <tr><td colspan="6"><div class="empty"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No applications match these filters.</p></div></td></tr>
            <?php endif; ?>
            <?php foreach ($items as $a): ?>
                <tr class="reveal">
                    <td>
                        <a class="fw-semibold text-decoration-none" href="<?= site_url('responses/view/' . $a['id']) ?>"><?= esc($a['name']) ?></a>
                        <div class="small text-muted"><?= esc($a['email']) ?></div>
                    </td>
                    <td><?= esc($a['job_name'] ?? '—') ?></td>
                    <td class="text-nowrap"><?= esc($a['phone']) ?></td>
                    <td><span class="badge-soft st-<?= esc($a['status']) ?>"><?= esc($a['status']) ?></span></td>
                    <td class="small text-muted text-nowrap"><?= date('d M Y', strtotime($a['created_at'])) ?><br><?= date('h:i A', strtotime($a['created_at'])) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-light border" href="<?= site_url('responses/view/' . $a['id']) ?>" title="View"><i class="bi bi-eye"></i></a>
                        <a class="btn btn-sm btn-light border" href="<?= site_url('responses/resume/' . $a['id']) ?>" title="Download resume"><i class="bi bi-file-earmark-arrow-down"></i></a>
                        <button class="btn btn-sm btn-light border text-danger" title="Delete"
                                data-delete-url="<?= site_url('responses/delete/' . $a['id']) ?>"
                                data-delete-name="the application from <?= esc($a['name']) ?>"><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($items): ?>
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 border-top">
            <div class="small text-muted">Showing <?= $from ?>–<?= $to ?> of <?= $total ?></div>
            <?= $pager->links('default', 'bootstrap_full') ?>
            <div class="d-none d-md-block" style="width:120px"></div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
