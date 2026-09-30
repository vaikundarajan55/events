<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $editing = ! empty($item); ?>
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header"><?= $editing ? 'Edit image' : 'Upload images' ?></div>
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data" data-loading
                      action="<?= $editing ? site_url('gallery/update/' . $item['id']) : site_url('gallery/store') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label" for="title">Title <?= $editing ? '' : '<span class="text-muted fw-normal">(optional — the file name is used when empty)</span>' ?></label>
                        <input type="text" class="form-control" id="title" name="title" maxlength="150"
                               value="<?= esc(old('title', $item['title'] ?? '', false)) ?>" <?= $editing ? 'required' : '' ?>>
                    </div>

                    <?php if ($editing): ?>
                        <div class="mb-3">
                            <label class="form-label">Current image</label><br>
                            <img src="<?= esc($siteUrl . 'uploads/gallery/' . ($item['thumb'] ? 'thumbs/' . $item['thumb'] : $item['image'])) ?>"
                                 class="rounded border" style="max-width:240px" alt="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="image">Replace image <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Images <span class="text-muted fw-normal">(JPG, PNG, WEBP, GIF · up to 4 MB each)</span></label>
                            <div class="dropzone" id="dropzone">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <div class="fw-semibold mt-2" id="dzCount">Drag &amp; drop or click to browse</div>
                                <input type="file" name="images[]" accept="image/*" multiple hidden>
                            </div>
                            <div class="preview-grid" id="previewGrid"></div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label" for="status">Visibility on website</label>
                        <select name="status" id="status" class="form-select" style="max-width:220px">
                            <?php $cur = (string) old('status', $item['status'] ?? '1'); ?>
                            <option value="1" <?= $cur === '1' ? 'selected' : '' ?>>Visible</option>
                            <option value="0" <?= $cur === '0' ? 'selected' : '' ?>>Hidden</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><?= $editing ? 'Save changes' : 'Upload' ?></button>
                        <a href="<?= site_url('gallery') ?>" class="btn btn-light border">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
