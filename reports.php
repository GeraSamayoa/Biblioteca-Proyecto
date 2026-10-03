<?php
/* ============================================================
 * reports.php — Consultas de préstamos
 * Pestañas por estado (Activo, Vencido, Devuelto) y un filtro
 * de texto para encontrar a un estudiante o un libro.
 * ============================================================ */
require __DIR__ . '/app/init.php';

$tabs = [
    ''              => 'Todos',
    Loan::ACTIVE    => 'Activos',
    Loan::OVERDUE   => 'Vencidos',
    Loan::RETURNED  => 'Devueltos',
];

$status = array_key_exists(param('status'), $tabs) ? param('status') : '';
$text   = param('text');
$rows   = $loans->filter($status, $text);

// Arma el enlace de una pestaña conservando el texto buscado
$tabUrl = fn(string $s) => 'reports.php?' . http_build_query(array_filter(['status' => $s, 'text' => $text]));

$pageTitle = 'Consultas';
$section   = 'reports';
include __DIR__ . '/partials/top.php';
?>

<p class="text-uppercase small fw-semibold text-primary mb-1">Historial</p>
<h1 class="h2 mb-4">Consulta de préstamos</h1>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom mb-4">
    <ul class="nav nav-underline">
        <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $status === $key ? 'active' : 'text-secondary' ?>" href="<?= h($tabUrl($key)) ?>">
                    <?= $label ?> <span class="badge rounded-pill text-bg-light border ms-1"><?= count($loans->filter($key)) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <form method="get" class="d-flex gap-2 pb-2">
        <?php if ($status): ?><input type="hidden" name="status" value="<?= h($status) ?>"><?php endif; ?>
        <input type="search" name="text" value="<?= h($text) ?>" class="form-control form-control-sm" placeholder="Carné, nombre o libro" aria-label="Buscar en préstamos">
        <button class="btn btn-sm btn-primary" aria-label="Buscar"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>

<?php if ($status === Loan::OVERDUE && $rows): ?>
    <div class="alert alert-danger small"><i class="fa-solid fa-bell me-2"></i>
        Estos préstamos superaron la fecha prevista de devolución y siguen sin regresar.
    </div>
<?php endif; ?>

<?php if (!$rows): ?>
    <p class="text-secondary py-4 text-center"><i class="fa-regular fa-folder-open me-2"></i>No hay préstamos para mostrar.</p>
<?php else: ?>
    <div class="table-responsive bg-white border rounded-3 shadow-sm">
        <table class="table table-borderless table-striped align-middle mb-0">
            <thead class="border-bottom">
                <tr class="small text-secondary">
                    <th class="ps-3">Préstamo</th><th>Libro</th><th>Estudiante</th><th>Periodo</th><th>Devuelto</th><th class="pe-3">Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $loan): ?>
                <tr>
                    <td class="ps-3 fw-semibold text-nowrap"><?= h($loan->code) ?></td>
                    <td class="text-primary fw-semibold text-nowrap"><?= h($loan->bookCode) ?></td>
                    <td><?= h($loan->studentName) ?> <small class="text-secondary d-block"><?= h($loan->studentId) ?></small></td>
                    <td class="small text-nowrap"><?= nice_date($loan->loanDate) ?> <i class="fa-solid fa-arrow-right-long text-secondary mx-1"></i> <?= nice_date($loan->dueDate) ?></td>
                    <td class="small text-nowrap"><?= nice_date($loan->returnDate) ?></td>
                    <td class="pe-3"><?php include __DIR__ . '/partials/status_badge.php'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/bottom.php'; ?>
