<?php
declare(strict_types=1);

/** HTML-escape. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ---------------- Flash messages ---------------- */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/* ---------------- CSRF ---------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $sent = (string) ($_POST['csrf'] ?? '');
    return $sent !== '' && hash_equals((string) ($_SESSION['csrf'] ?? ''), $sent);
}

/* ---------------- Old form input ---------------- */
function old(string $key, string $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

/* ---------------- Gallery image URLs ---------------- */
function gallery_thumb_url(array $g): string
{
    return !empty($g['thumb']) ? 'uploads/gallery/thumbs/' . rawurlencode($g['thumb'])
                               : 'uploads/gallery/' . rawurlencode($g['image']);
}

function gallery_full_url(array $g): string
{
    return 'uploads/gallery/' . rawurlencode($g['image']);
}

/* ---------------- Pagination (Bootstrap 5 markup) ---------------- */
function paginate_url(string $file, array $params, int $page): string
{
    $params['page'] = $page;
    return $file . '?' . http_build_query(array_filter($params, static fn ($v) => $v !== '' && $v !== null));
}

function render_pagination(string $file, array $params, int $page, int $pages): string
{
    if ($pages <= 1) {
        return '';
    }
    $item = static function (string $label, int $target, bool $disabled = false, bool $active = false, string $aria = '') use ($file, $params): string {
        $cls = 'page-item' . ($disabled ? ' disabled' : '') . ($active ? ' active' : '');
        $aria = $aria !== '' ? ' aria-label="' . e($aria) . '"' : '';
        return '<li class="' . $cls . '"><a class="page-link" href="' . e(paginate_url($file, $params, $target)) . '"' . $aria . '>' . $label . '</a></li>';
    };

    $html  = '<nav aria-label="Pagination"><ul class="pagination justify-content-center flex-wrap">';
    $html .= $item('&lsaquo;', max(1, $page - 1), $page <= 1, false, 'Previous');
    $from = max(1, $page - 2);
    $to   = min($pages, $page + 2);
    if ($from > 1) {
        $html .= $item('1', 1);
        if ($from > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
    }
    for ($i = $from; $i <= $to; $i++) {
        $html .= $item((string) $i, $i, false, $i === $page);
    }
    if ($to < $pages) {
        if ($to < $pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
        $html .= $item((string) $pages, $pages);
    }
    $html .= $item('&rsaquo;', min($pages, $page + 1), $page >= $pages, false, 'Next');
    return $html . '</ul></nav>';
}

function current_page_number(): int
{
    return max(1, (int) ($_GET['page'] ?? 1));
}
