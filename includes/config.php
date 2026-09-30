<?php
/**
 * Front-end configuration (core PHP).
 * Edit the DB values to match your MySQL server.
 */
declare(strict_types=1);

define('SITE_NAME', 'BrightPath');
define('SITE_TAGLINE', 'A small studio that builds useful web products.');
define('CONTACT_EMAIL', 'hello@example.com');

// ---- MySQL ----
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'ecommerce_careerhub_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// ---- Uploads (shared with the CI4 admin panel) ----
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_DIR', ROOT_PATH . '/uploads');
define('MAX_RESUME_BYTES', 2 * 1024 * 1024); // 2 MB

// ---- Paths used in links ----
define('ADMIN_URL', 'admin/');

// ---- Pagination ----
define('GALLERY_PER_PAGE', 9);
define('CAREERS_PER_PAGE', 5);

date_default_timezone_set('Asia/Kolkata');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}
