<footer class="site-footer">
    <div class="container">
        <div class="row gy-3 align-items-center">
            <div class="col-md-6">
                <div class="fw-bold fs-5"><?= e(SITE_NAME) ?></div>
                <div class="text-white-50"><?= e(SITE_TAGLINE) ?></div>
            </div>
            <div class="col-md-6 text-md-end">
                <a class="footer-link" href="gallery.php">Gallery</a>
                <a class="footer-link" href="career.php">Careers</a>
                <a class="footer-link" href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="small text-white-50">&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="assets/js/main.js"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
