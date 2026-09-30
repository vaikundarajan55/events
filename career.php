<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

/* =====================================================================
 *  POST  -  handle a job application
 * ===================================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fail = static function (string $msg, ?int $jobId = null): never {
        $_SESSION['old']      = array_intersect_key($_POST, array_flip(['name', 'email', 'phone', 'message']));
        $_SESSION['open_job'] = $jobId;
        flash_set('error', $msg);
        header('Location: career.php' . (!empty($_SESSION['return_qs']) ? '?' . $_SESSION['return_qs'] : ''));
        exit;
    };

    $jobId = (int) ($_POST['career_id'] ?? 0);

    if (!csrf_valid()) {
        $fail('Your session expired. Please try again.', $jobId);
    }
    // Honeypot: real users never fill this hidden field
    if (!empty($_POST['website'])) {
        flash_set('success', 'Thanks! Your application has been received.');
        header('Location: career.php');
        exit;
    }
    // Basic throttling: max 3 applications / 10 minutes per session
    $_SESSION['apply_log'] = array_filter($_SESSION['apply_log'] ?? [], static fn ($t) => $t > time() - 600);
    if (count($_SESSION['apply_log']) >= 3) {
        $fail('You have applied several times in a short while. Please wait a few minutes.', $jobId);
    }

    $name    = trim((string) ($_POST['name'] ?? ''));
    $email   = strtolower(trim((string) ($_POST['email'] ?? '')));
    $phone   = trim((string) ($_POST['phone'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    $errors = [];
    $stmt = $pdo->prepare('SELECT id, title FROM careers WHERE id = ? AND status = 1');
    $stmt->execute([$jobId]);
    $job = $stmt->fetch();

    if (!$job)                                              $errors[] = 'This position is no longer open.';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100)     $errors[] = 'Enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone))      $errors[] = 'Enter a valid phone number (digits, spaces, + - ( ) only).';
    if (mb_strlen($message) > 2000)                         $errors[] = 'Cover note must be 2000 characters or fewer.';

    // ---- Resume ----
    $file = $_FILES['resume'] ?? null;
    $ext  = '';
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Attach your resume (PDF, DOC or DOCX).';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? 'Resume is too large (max 2 MB).' : 'Resume upload failed. Please try again.';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = [
            'pdf'  => ['application/pdf'],
            'doc'  => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
        ];
        if ($file['size'] > MAX_RESUME_BYTES) {
            $errors[] = 'Resume is too large (max 2 MB).';
        } elseif (!isset($allowed[$ext])) {
            $errors[] = 'Resume must be a PDF, DOC or DOCX file.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
            if (!in_array($mime, $allowed[$ext], true)) {
                $errors[] = 'That file does not look like a valid ' . strtoupper($ext) . ' document.';
            }
        }
    }

    // Duplicate check
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE career_id = ? AND email = ?');
        $stmt->execute([$jobId, $email]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'You have already applied for this position with that email address.';
        }
    }

    if ($errors) {
        $fail(implode(' ', $errors), $jobId);
    }

    // ---- Save file + row ----
    $dir = UPLOAD_DIR . '/resumes';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        $fail('Server could not store the resume. Please contact us by email.', $jobId);
    }
    $stored = bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $stored)) {
        $fail('Server could not store the resume. Please try again.', $jobId);
    }

    $now  = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare(
        'INSERT INTO applications (career_id, job_title, name, email, phone, message, resume, status, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, "New", ?, ?)'
    );
    $stmt->execute([$jobId, $job['title'], $name, $email, $phone, $message !== '' ? $message : null, $stored, $now, $now]);

    $_SESSION['apply_log'][] = time();
    unset($_SESSION['old'], $_SESSION['open_job']);
    flash_set('success', 'Thanks ' . explode(' ', $name)[0] . '! Your application for ' . $job['title'] . ' has been received.');
    header('Location: career.php');
    exit;
}

/* =====================================================================
 *  GET  -  list open jobs (search + filter + pagination)
 * ===================================================================== */
$q     = trim((string) ($_GET['q'] ?? ''));
$type  = (string) ($_GET['type'] ?? '');
$types = ['Full Time', 'Part Time', 'Contract', 'Internship'];
$page  = current_page_number();

$where  = 'WHERE status = 1';
$params = [];
if ($q !== '') {
    // Native prepared statements can't reuse a named placeholder, so use one per column
    $where .= ' AND (title LIKE :q1 OR department LIKE :q2 OR location LIKE :q3)';
    $like = '%' . $q . '%';
    $params[':q1'] = $params[':q2'] = $params[':q3'] = $like;
}
if (in_array($type, $types, true)) {
    $where .= ' AND job_type = :t';
    $params[':t'] = $type;
} else {
    $type = '';
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM careers $where");
$stmt->execute($params);
$total  = (int) $stmt->fetchColumn();
$pages  = max(1, (int) ceil($total / CAREERS_PER_PAGE));
$page   = min($page, $pages);
$offset = ($page - 1) * CAREERS_PER_PAGE;

$stmt = $pdo->prepare("SELECT * FROM careers $where ORDER BY id DESC LIMIT " . CAREERS_PER_PAGE . " OFFSET $offset");
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$_SESSION['return_qs'] = http_build_query(array_filter(['q' => $q, 'type' => $type, 'page' => $page > 1 ? $page : null]));

$openJob = isset($_SESSION['open_job']) ? (int) $_SESSION['open_job'] : 0;
$openJobTitle = '';
foreach ($jobs as $j) {
    if ((int) $j['id'] === $openJob) { $openJobTitle = $j['title']; }
}

$pageTitle = 'Careers';
$pageDesc  = 'Open positions at ' . SITE_NAME . '.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <h1 class="page-title">Careers</h1>
        <p class="text-muted mb-0">Find a role, read the details and apply in under two minutes.</p>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <form class="row g-2 mb-4" method="get" action="career.php">
            <div class="col-md-5 col-lg-4">
                <input type="search" name="q" class="form-control" placeholder="Search title, team or location…" value="<?= e($q) ?>">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">All types</option>
                    <?php foreach ($types as $t): ?>
                        <option <?= $type === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <button class="btn btn-ink w-100">Search</button>
            </div>
            <?php if ($q !== '' || $type !== ''): ?>
                <div class="col-12 col-md-auto"><a href="career.php" class="btn btn-link text-muted">Clear filters</a></div>
            <?php endif; ?>
            <div class="col-12 col-md text-md-end small text-muted align-self-center"><?= $total ?> open position<?= $total === 1 ? '' : 's' ?></div>
        </form>

        <?php if (!$jobs): ?>
            <div class="empty-state"><i class="bi bi-briefcase"></i><p class="mb-0">No open positions match your search.</p></div>
        <?php endif; ?>

        <div class="row g-3">
            <?php foreach ($jobs as $i => $j): $cid = 'job' . (int) $j['id']; ?>
                <div class="col-12" data-aos="fade-up" data-aos-delay="<?= $i * 70 ?>">
                    <article class="job-card">
                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-center">
                            <div>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="chip"><?= e($j['job_type']) ?></span>
                                    <?php if ($j['experience']): ?><span class="chip chip-soft"><?= e($j['experience']) ?></span><?php endif; ?>
                                </div>
                                <h2 class="job-title"><?= e($j['title']) ?></h2>
                                <div class="text-muted small">
                                    <i class="bi bi-diagram-3 me-1"></i><?= e($j['department']) ?>
                                    <span class="mx-2">|</span>
                                    <i class="bi bi-geo-alt me-1"></i><?= e($j['location']) ?>
                                    <span class="mx-2">|</span>
                                    Posted <?= date('d M Y', strtotime($j['created_at'])) ?>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-shrink-0">
                                <button class="btn btn-outline-ink" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $cid ?>" aria-expanded="false" aria-controls="<?= $cid ?>">Details</button>
                                <button class="btn btn-ink btn-apply" type="button" data-job-id="<?= (int) $j['id'] ?>" data-job-title="<?= e($j['title']) ?>">Apply now</button>
                            </div>
                        </div>
                        <div class="collapse" id="<?= $cid ?>">
                            <hr>
                            <div class="job-desc"><?= nl2br(e($j['description'])) ?></div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-5"><?= render_pagination('career.php', ['q' => $q, 'type' => $type], $page, $pages) ?></div>
    </div>
</section>

<!-- Apply modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyTitle" aria-hidden="true" data-open-job="<?= $openJobTitle !== '' ? (int) $openJob : 0 ?>">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="post" action="career.php" enctype="multipart/form-data" id="applyForm" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="career_id" id="careerId" value="">
            <div class="d-none" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="modal-title h4" id="applyTitle">Apply for this role</h2>
                    <div class="text-muted small" id="applyJob"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="f_name">Full name</label>
                    <input type="text" class="form-control" id="f_name" name="name" maxlength="100" required value="<?= old('name') ?>" autocomplete="name">
                    <div class="invalid-feedback">Please enter your name.</div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="f_email">Email</label>
                        <input type="email" class="form-control" id="f_email" name="email" maxlength="150" required value="<?= old('email') ?>" autocomplete="email">
                        <div class="invalid-feedback">Enter a valid email.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="f_phone">Phone</label>
                        <input type="tel" class="form-control" id="f_phone" name="phone" maxlength="20" required pattern="[0-9+\-\s()]{7,20}" value="<?= old('phone') ?>" autocomplete="tel">
                        <div class="invalid-feedback">Enter a valid phone number.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="f_resume">Resume <span class="text-muted fw-normal">(PDF, DOC, DOCX · max 2 MB)</span></label>
                    <input type="file" class="form-control" id="f_resume" name="resume" accept=".pdf,.doc,.docx" required>
                    <div class="invalid-feedback" id="resumeFeedback">Attach your resume.</div>
                </div>
                <div>
                    <label class="form-label" for="f_msg">Cover note <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea class="form-control" id="f_msg" name="message" rows="4" maxlength="2000"><?= old('message') ?></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-ink px-4">Submit application</button>
            </div>
        </form>
    </div>
</div>
<?php
unset($_SESSION['old'], $_SESSION['open_job']);
require __DIR__ . '/includes/footer.php';
