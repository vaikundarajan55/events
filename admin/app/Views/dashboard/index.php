<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$cards = [
    ['Gallery images',   $totalGallery, 'bi-images',     't1', 'gallery'],
    ['Open positions',   $openJobs,     'bi-briefcase',  't2', 'careers'],
    ['Applications',     $totalApps,    'bi-inbox',      't3', 'responses'],
    ['Awaiting review',  $newApps,      'bi-bell',       't4', 'responses?status=New'],
];
?>
<div class="row g-3 mb-4">
    <?php foreach ($cards as $i => [$label, $val, $icon, $tone, $link]): ?>
        <div class="col-6 col-xl-3 reveal" style="--i:<?= $i ?>">
            <a href="<?= site_url($link) ?>" class="card stat text-decoration-none text-reset h-100">
                <div class="stat-icon <?= $tone ?>"><i class="bi <?= $icon ?>"></i></div>
                <div>
                    <div class="stat-value" data-count="<?= (int) $val ?>">0</div>
                    <div class="stat-label"><?= esc($label) ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8 reveal" style="--i:4">
        <div class="card h-100">
            <div class="card-header">Applications, last 7 days</div>
            <div class="card-body"><div style="height:260px"><canvas id="appsChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4 reveal" style="--i:5">
        <div class="card h-100">
            <div class="card-header">Pipeline</div>
            <div class="card-body">
                <?php foreach ($statuses as $name => $count):
                    $pct = $totalApps ? round($count / $totalApps * 100) : 0; ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold"><?= esc($name) ?></span>
                            <span class="text-muted"><?= $count ?> · <?= $pct ?>%</span>
                        </div>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar" style="width:<?= $pct ?>%;background:var(--teal)"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="card reveal" style="--i:6">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Latest applications</span>
        <a href="<?= site_url('responses') ?>" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Applicant</th><th>Position</th><th>Status</th><th>Applied</th></tr></thead>
            <tbody>
            <?php if (! $recent): ?>
                <tr><td colspan="4"><div class="empty"><i class="bi bi-inbox"></i><p class="mb-0 mt-2">No applications yet. They will appear here when someone applies on the website.</p></div></td></tr>
            <?php endif; ?>
            <?php foreach ($recent as $r): ?>
                <tr class="reveal">
                    <td>
                        <a class="fw-semibold text-decoration-none" href="<?= site_url('responses/view/' . $r['id']) ?>"><?= esc($r['name']) ?></a>
                        <div class="small text-muted"><?= esc($r['email']) ?></div>
                    </td>
                    <td><?= esc($r['job_name'] ?? '—') ?></td>
                    <td><span class="badge-soft st-<?= esc($r['status']) ?>"><?= esc($r['status']) ?></span></td>
                    <td class="text-muted small"><?= date('d M Y, h:i A', strtotime($r['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('appsChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Applications',
            data: <?= json_encode($chartValues) ?>,
            backgroundColor: '#0e8f83',
            borderRadius: 8,
            maxBarThickness: 44
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 900, easing: 'easeOutCubic' },
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef2f1' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
<?= $this->endSection() ?>
