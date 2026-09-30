# CareerHub — CodeIgniter 4 Admin + Core PHP Website

| Part | Tech | Folder |
|---|---|---|
| **Admin panel** | CodeIgniter 4.7.4 (full framework included), Bootstrap 5.3, MySQL | `admin/` |
| **Public website** (Home, Gallery, Careers + apply form) | Core PHP 8 (PDO), Bootstrap 5.3, AOS animations | project root |
| **Database** | MySQL / MariaDB | `database/careerhub_db.sql` |
| **Shared uploads** | Gallery images + resumes (used by both apps) | `uploads/` |

## Folder structure

```
careerhub/
├── index.php  gallery.php  career.php      ← core PHP front end
├── includes/    config.php (DB), db.php, functions.php, header.php, footer.php
├── assets/      css/style.css, js/main.js
├── uploads/     gallery/ (public), gallery/thumbs/, resumes/ (blocked from web)
├── database/    careerhub_db.sql
└── admin/                                   ← CodeIgniter 4 admin
    ├── app/
    │   ├── Controllers/  Auth, Dashboard, Gallery, Careers, Responses
    │   ├── Models/       AdminModel, GalleryModel, CareerModel, ApplicationModel
    │   ├── Filters/      AuthFilter, GuestFilter
    │   ├── Config/       Routes, Filters, Pager, Site, App …
    │   └── Views/        layouts/, auth/, dashboard/, gallery/, careers/, responses/, pagers/
    ├── public/           index.php (web root), assets/css/admin.css, assets/js/admin.js
    ├── system/           CodeIgniter framework core
    ├── writable/         cache, logs, session  (must be writable)
    └── .env              base URL + DB credentials
```

## Requirements
* PHP **8.2+** with extensions: `intl`, `mbstring`, `mysqlnd/mysqli`, `pdo_mysql`, `gd`, `fileinfo`
  (XAMPP: open `php.ini` and remove the `;` before `extension=intl`, `extension=gd`, `extension=fileinfo`, then restart Apache)
* MySQL 5.7+ / MariaDB 10.3+
* Apache with `mod_rewrite` (XAMPP / WAMP / Laragon all work)

## Setup (XAMPP / WAMP / Laragon)
1. Copy the **`careerhub`** folder to `htdocs` (or `www`).
2. Open phpMyAdmin → **Import** → choose `database/careerhub_db.sql`. This creates the `careerhub_db` database with tables and sample data.
3. Database credentials — edit **both** places if yours differ from `root` / *(empty)*:
   * Front end: `includes/config.php`
   * Admin: `admin/.env` (`database.default.*`)
4. If the folder is not named `events` or not at `http://localhost/events/`, update:
   * `admin/.env` → `app.baseURL` and `site.siteUrl`
5. Open:
   * Website — `http://localhost/events/`
   * Admin — `http://localhost/events/admin/`

**Default admin login:** `admin@example.com` / `admin123`  → change it right away (see below).

## Admin features
* **Login / Logout** — hashed passwords, CSRF protection, lock-out after 5 wrong attempts (5 min).
* **Dashboard** — animated counters, 7-day applications chart, pipeline breakdown, latest applications.
* **Gallery** — multi-image drag-and-drop upload, auto thumbnails, edit, show/hide, delete, search, pagination (8 / page).
* **Careers** — post / edit / close / delete jobs, applicant count per job, search + filter, pagination.
* **Career responses** — search, filter by job and status, pagination (10 / page), view details, download resume,
  change status (New → Reviewed → Shortlisted / Rejected), delete, **CSV export**.
* Fully responsive (sidebar becomes a slide-in menu on mobile).

## Website features
* **Home** — hero collage from latest gallery photos, gallery preview, latest jobs.
* **Gallery** — search, pagination (9 / page), lightbox with keyboard/swipe navigation.
* **Careers** — search + type filter, pagination (5 / page), expandable job details, **Apply modal**
  (name, email, phone, resume PDF/DOC/DOCX ≤ 2 MB, cover note). Saved to MySQL and visible in the admin instantly.
* Security: CSRF token, honeypot, per-session rate limit, duplicate-application check, server-side MIME check on resumes.

## Change the admin password
Generate a hash (run in a terminal, or use any bcrypt tool):
```
php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
```
Then in phpMyAdmin run:
```sql
UPDATE admins SET password = 'PASTE_HASH_HERE' WHERE email = 'admin@example.com';
```

## Going live checklist
* `admin/.env` → `CI_ENVIRONMENT = production`, set the real `app.baseURL` (https) and DB credentials.
* Better: point a virtual host / subdomain at `admin/public` (so `admin/app`, `admin/system` are outside the web root).
* Make `admin/writable/` and `uploads/` writable by the web server user.
* Keep `uploads/resumes/.htaccess` (blocks direct downloads). On Nginx add: `location ^~ /uploads/resumes/ { deny all; }`
* Change the default admin password and the DB password. Delete the sample gallery images if you don't want them.

## Notes
* Sample data (14 gallery photos, 6 jobs, 15 applications) is included so pagination and animations can be seen immediately.
  Delete it from the admin panel when ready.
* Bootstrap, Bootstrap Icons, AOS, Chart.js and Google Fonts load from CDNs — an internet connection is needed for styling.
