<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <form class="d-flex flex-wrap gap-2" method="get" action="<?= site_url('careers') ?>">
        <input type="search" name="q" value="<?= esc($q) ?>" class="form-control" style="max-width:260px" placeholder="Search title, department, location…">
        <select name="status" class="form-select" style="max-width:150px" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Open</option>
            <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Closed</option>
        </select>
        <button class="btn btn-light border"><i class="bi bi-search"></i></button>
    </form>
    <a href="<?= site_url('careers/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Post a job</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>Position</th><th>Department</th><th>Location</th><th>Type</th><th class="text-center">Applicants</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (! $items): ?>
                <tr><td colspan="7"><div class="empty"><i class="bi bi-briefcase"></i><p class="mt-2 mb-0">No jobs found.</p></div></td></tr>
            <?php endif; ?>
            <?php foreach ($items as $j): ?>
                <tr class="reveal">
                    <td>
                        <div class="fw-semibold"><?= esc($j['title']) ?></div>
                        <div class="small text-muted"><?= $j['experience'] ? esc($j['experience']) . ' exp · ' : '' ?>Posted <?= date('d M Y', strtotime($j['created_at'])) ?></div>
                    </td>
                    <td><?= esc($j['department']) ?></td>
                    <td><?= esc($j['location']) ?></td>
                    <td><?= esc($j['job_type']) ?></td>
                    <td class="text-center">
                        <a href="<?= site_url('responses?job=' . $j['id']) ?>" class="badge-soft st-Reviewed text-decoration-none"><?= (int) $j['applicants'] ?></a>
                    </td>
                    <td><span class="badge-soft <?= $j['status'] ? 'st-on' : 'st-off' ?>"><?= $j['status'] ? 'Open' : 'Closed' ?></span></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-light border" href="<?= site_url('careers/edit/' . $j['id']) ?>" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="<?= site_url('careers/toggle/' . $j['id']) ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-light border" title="<?= $j['status'] ? 'Close job' : 'Reopen job' ?>"><i class="bi <?= $j['status'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i></button>
                        </form>
                        <button class="btn btn-sm btn-light border text-danger" title="Delete"
                                data-delete-url="<?= site_url('careers/delete/' . $j['id']) ?>"
                                data-delete-name="“<?= esc($j['title']) ?>”"><i class="bi bi-trash3"></i></button>
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
