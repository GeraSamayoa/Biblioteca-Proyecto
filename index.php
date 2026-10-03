<?php
/* ============================================================
 * index.php — Inicio
 * Portada con saludo y cifras del día, préstamos atrasados y
 * los últimos movimientos.
 * ============================================================ */
require __DIR__ . '/app/init.php';

$allBooks = $books->all();
$allLoans = $loans->all();

$summary = [
    'Títulos en catálogo'   => count($allBooks),
    'Ejemplares en estante' => array_sum(array_map(fn($b) => $b->available, $allBooks)),
    'Préstamos en curso'    => count($loans->filter(Loan::ACTIVE)),
    'Préstamos atrasados'   => count($loans->filter(Loan::OVERDUE)),
];
$overdue = $loans->filter(Loan::OVERDUE);
$recent  = array_slice($allLoans, 0, 5);

// Saludo según la hora
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Buenos días' : ($hour < 19 ? 'Buenas tardes' : 'Buenas noches');

$pageTitle = 'Inicio';
$section   = 'home';
include __DIR__ . '/partials/top.php';
?>

<!-- Portada: saludo, accesos y cifras del día -->
<section class="portada border border-primary-subtle rounded-4 p-4 p-lg-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <h1 class="display-6 mb-2 text-primary-emphasis"><?= $greeting ?>.</h1>
            <p class="text-body mb-4">
                Hoy hay <?= $summary['Préstamos en curso'] + $summary['Préstamos atrasados'] ?> libro(s) fuera de la biblioteca
                y <?= $summary['Préstamos atrasados'] ?> con la fecha de devolución vencida.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="loans.php" class="btn btn-primary">Prestar un libro</a>
                <a href="returns.php" class="btn btn-outline-primary bg-white">Recibir devolución</a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row row-cols-2 g-0 border border-primary-subtle rounded-3 bg-white bg-opacity-50">
                <?php $i = 0; foreach ($summary as $label => $value): ?>
                    <div class="col p-3 p-md-4 border-primary-subtle <?= $i % 2 === 0 ? 'border-end' : '' ?> <?= $i < 2 ? 'border-bottom' : '' ?>">
                        <div class="fs-1 font-serif lh-1 mb-1 text-primary-emphasis"><?= $value ?></div>
                        <div class="small text-secondary"><?= $label ?></div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
    </div>
</section>

<div class="row g-4">
    <section class="col-lg-6">
        <h2 class="h5 mb-3"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Atrasados</h2>
        <?php if (!$overdue): ?>
            <div class="border rounded-3 bg-white p-4 text-secondary">
                <i class="fa-regular fa-face-smile me-2"></i>Todos los préstamos están al día.
            </div>
        <?php else: ?>
            <ul class="list-group shadow-sm">
                <?php foreach ($overdue as $loan): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-semibold"><?= h($loan->studentName) ?></div>
                            <small class="text-secondary"><?= h($loan->bookCode) ?> · venció el <?= nice_date($loan->dueDate) ?></small>
                        </div>
                        <span class="badge text-bg-danger rounded-1"><?= $loan->daysLate() ?> día(s)</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="col-lg-6">
        <div class="d-flex justify-content-between align-items-baseline mb-3">
            <h2 class="h5 mb-0"><i class="fa-regular fa-clock text-primary me-2"></i>Movimientos recientes</h2>
            <a href="reports.php" class="small">Ver todo</a>
        </div>
        <?php if (!$recent): ?>
            <div class="border rounded-3 bg-white p-4 text-secondary">Todavía no se ha registrado ningún préstamo.</div>
        <?php else: ?>
            <ul class="list-group shadow-sm">
                <?php foreach ($recent as $loan): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-semibold"><?= h($loan->code) ?> · <?= h($loan->bookCode) ?></div>
                            <small class="text-secondary"><?= h($loan->studentName) ?> · <?= nice_date($loan->loanDate) ?></small>
                        </div>
                        <?php include __DIR__ . '/partials/status_badge.php'; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

<?php include __DIR__ . '/partials/bottom.php'; ?>
