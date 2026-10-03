<?php

$styles = [
    'ok'    => ['text-bg-success', 'fa-solid fa-circle-check'],
    'error' => ['text-bg-danger',  'fa-solid fa-circle-exclamation'],
    'info'  => ['text-bg-primary', 'fa-solid fa-circle-info'],
];
$notices = Flash::pullAll();
?>
<?php if ($notices): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php foreach ($notices as $n): [$bg, $icon] = $styles[$n['type']] ?? $styles['info']; ?>
            <div class="toast align-items-center border-0 <?= $bg ?>" role="status" aria-live="polite" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body"><i class="<?= $icon ?> me-2"></i><?= h($n['message']) ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
