<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(2);
?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($pager->getFirstPageNumber() !== $pager->getCurrentPageNumber()) : ?>
            <li class="page-item"><a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="First page"><i class="bi bi-chevron-double-left"></i></a></li>
        <?php endif ?>

        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPreviousPage() ? $pager->getPreviousPage() : '#' ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
        </li>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>

        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNextPage() ? $pager->getNextPage() : '#' ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
        </li>

        <?php if ($pager->getLastPageNumber() !== $pager->getCurrentPageNumber()) : ?>
            <li class="page-item"><a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Last page"><i class="bi bi-chevron-double-right"></i></a></li>
        <?php endif ?>
    </ul>
</nav>
