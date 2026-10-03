<?php

$badgeStyle = match ($loan->status()) {
    Loan::OVERDUE  => ['text-bg-danger',  'fa-solid fa-hourglass-end'],
    Loan::RETURNED => ['text-bg-light border', 'fa-solid fa-check'],
    default        => ['text-bg-primary', 'fa-regular fa-clock'],
};
?>
<span class="badge rounded-1 fw-semibold <?= $badgeStyle[0] ?>">
    <i class="<?= $badgeStyle[1] ?> me-1"></i><?= $loan->status() ?>
</span>
