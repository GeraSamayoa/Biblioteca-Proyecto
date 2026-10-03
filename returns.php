<?php
/* ============================================================
 * returns.php — Registrar devoluciones
 * Cada préstamo pendiente aparece como una tarjeta con su botón
 * "Recibir libro". También se puede filtrar por carné o nombre.
 * ============================================================ */
require __DIR__ . '/app/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanCode = field('loan');
    $problem  = $loanCode === '' ? 'Indica el código del préstamo.' : $library->receive($loanCode);

    $problem ? Flash::error($problem) : Flash::ok("Devolución del préstamo $loanCode registrada. El ejemplar ya está disponible.");
    go('returns.php');
}

$who = param('who');
$pending = array_filter($loans->open(), fn(Loan $l) => $who === ''
    || contains($l->studentId, $who) || contains($l->studentName, $who) || contains($l->code, $who));

// Para mostrar el título del libro en cada tarjeta
$titles = [];
foreach ($books->all() as $b) {
    $titles[$b->code] = $b->title;
}

$pageTitle = 'Devoluciones';
$section   = 'returns';
include __DIR__ . '/partials/top.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="text-uppercase small fw-semibold text-primary mb-1">Circulación</p>
        <h1 class="h2 mb-0">Recibir libros devueltos</h1>
    </div>
    <form method="get" class="d-flex gap-2" role="search">
        <input type="search" name="who" value="<?= h($who) ?>" class="form-control" placeholder="Carné, nombre o PR-0000" aria-label="Filtrar préstamos">
        <button class="btn btn-outline-primary" aria-label="Filtrar"><i class="fa-solid fa-filter"></i></button>
    </form>
</div>

<?php if (!$pending): ?>
    <div class="text-center bg-white border rounded-3 py-5 text-secondary">
        <i class="fa-regular fa-circle-check fs-2 d-block mb-2 text-success"></i>
        <?= $who ? 'Ningún préstamo pendiente coincide con el filtro.' : 'No hay libros pendientes de devolución.' ?>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xxl-3 g-3">
        <?php foreach ($pending as $loan): ?>
            <div class="col">
                <article class="h-100 bg-white border rounded-3 shadow-sm p-4 d-flex flex-column <?= $loan->status() === Loan::OVERDUE ? 'border-danger-subtle' : '' ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="small fw-semibold text-secondary"><?= h($loan->code) ?></span>
                        <?php include __DIR__ . '/partials/status_badge.php'; ?>
                    </div>
                    <h2 class="h5 mb-1"><?= h($titles[$loan->bookCode] ?? $loan->bookCode) ?></h2>
                    <p class="small text-primary fw-semibold mb-3"><?= h($loan->bookCode) ?></p>

                    <ul class="list-unstyled small text-secondary mb-4">
                        <li><i class="fa-regular fa-user fa-fw me-1"></i><?= h($loan->studentName) ?> · <?= h($loan->studentId) ?></li>
                        <li><i class="fa-regular fa-calendar fa-fw me-1"></i>Debe volver el <?= nice_date($loan->dueDate) ?>
                            <?php if ($loan->daysLate()): ?><span class="text-danger">(<?= $loan->daysLate() ?> día(s) tarde)</span><?php endif; ?>
                        </li>
                    </ul>

                    <form method="post" class="mt-auto" data-ask="¿Confirmas que el libro del préstamo <?= h($loan->code) ?> fue devuelto?">
                        <input type="hidden" name="loan" value="<?= h($loan->code) ?>">
                        <button class="btn btn-outline-primary w-100"><i class="fa-solid fa-arrow-rotate-left me-2"></i>Recibir libro</button>
                    </form>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/bottom.php'; ?>
