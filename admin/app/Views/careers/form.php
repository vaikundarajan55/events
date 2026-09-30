<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$editing = ! empty($job);
$v = static fn (string $k, $d = '') => esc(old($k, $job[$k] ?? $d, false));
?>
<div class="row">
    <div class="col-xl-9">
        <div class="card">
            <div class="card-header"><?= $editing ? 'Edit job' : 'New job opening' ?></div>
            <div class="card-body p-4">
                <form method="post" data-loading action="<?= $editing ? site_url('careers/update/' . $job['id']) : site_url('careers/store') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="title">Job title</label>
                            <input type="text" class="form-control" id="title" name="title" maxlength="150" value="<?= $v('title') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="department">Department</label>
                            <input type="text" class="form-control" id="department" name="department" maxlength="100" value="<?= $v('department') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" maxlength="100" value="<?= $v('location') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="job_type">Job type</label>
                            <select class="form-select" id="job_type" name="job_type" required>
                                <?php $cur = old('job_type', $job['job_type'] ?? 'Full Time', false);
                                foreach ($types as $t): ?>
                                    <option <?= $cur === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="experience">Experience</label>
                            <input type="text" class="form-control" id="experience" name="experience" maxlength="50" placeholder="e.g. 2–4 years" value="<?= $v('experience') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <?php $st = (string) old('status', $job['status'] ?? '1', false); ?>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= $st === '1' ? 'selected' : '' ?>>Open</option>
                                <option value="0" <?= $st === '0' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Description &amp; requirements</label>
                            <textarea class="form-control" id="description" name="description" rows="9" required><?= $v('description') ?></textarea>
                            <div class="form-text">Line breaks are kept on the website.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><?= $editing ? 'Save changes' : 'Post job' ?></button>
                        <a href="<?= site_url('careers') ?>" class="btn btn-light border">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
