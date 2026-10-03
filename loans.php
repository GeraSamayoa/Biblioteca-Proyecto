<?php

require __DIR__ . '/app/init.php';

$form = [
    'code'        => $loans->nextCode(),
    'bookCode'    => strtoupper(param('book')),
    'studentId'   => '',
    'studentName' => '',
    'loanDate'    => today(),
    'dueDate'     => date('Y-m-d', strtotime('+15 days')),
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($form) as $name) {
        $form[$name] = field($name);
    }
    $errors = $library->lend($form);

    if (!$errors) {
        Flash::ok("Préstamo {$form['code']} registrado correctamente.");
        go('loans.php');
    }
}

// Solo se ofrecen los libros que sí se pueden prestar
$lendable = array_filter($books->all(), fn(Book $b) => $b->isLendable());
$openLoans = $loans->open();

$invalid = fn(string $name) => isset($errors[$name]) ? 'is-invalid' : '';
$message = fn(string $name) => '<div class="invalid-feedback">' . h($errors[$name] ?? '') . '</div>';

$pageTitle = 'Préstamos';
$section   = 'loans';
include __DIR__ . '/partials/top.php';
?>

<p class="text-uppercase small fw-semibold text-primary mb-1">Circulación</p>
<h1 class="h2 mb-4">Prestar un libro</h1>

<form method="post" class="bg-white border rounded-3 shadow-sm p-4 mb-5" novalidate>
    <?php if (isset($errors['bookCode'])): ?>
        <div class="alert alert-danger py-2"><i class="fa-solid fa-ban me-2"></i><?= h($errors['bookCode']) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Paso 1 -->
        <div class="col-lg-4">
            <h2 class="h6 mb-3"><span class="badge rounded-circle text-bg-primary me-2">1</span>¿Qué libro?</h2>
            <label for="code" class="form-label small text-secondary">Código del préstamo *</label>
            <input type="text" id="code" name="code" class="form-control mb-3 <?= $invalid('code') ?>" value="<?= h($form['code']) ?>" maxlength="12" required>
            <?= $message('code') ?>

            <label for="bookCode" class="form-label small text-secondary">Libro *</label>
            <select id="bookCode" name="bookCode" class="form-select <?= $invalid('bookCode') ?>" required>
                <option value="">Selecciona un libro…</option>
                <?php foreach ($lendable as $b): ?>
                    <option value="<?= h($b->code) ?>" <?= strcasecmp($form['bookCode'], $b->code) === 0 ? 'selected' : '' ?>>
                        <?= h($b->code) ?> — <?= h($b->title) ?> (<?= $b->available ?> disp.)
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">Solo aparecen libros en circulación con ejemplares en estante.</div>
        </div>

        <!-- Paso 2 -->
        <div class="col-lg-4">
            <h2 class="h6 mb-3"><span class="badge rounded-circle text-bg-primary me-2">2</span>¿Quién lo lleva?</h2>
            <label for="studentId" class="form-label small text-secondary">Carné *</label>
            <input type="text" id="studentId" name="studentId" class="form-control mb-3 <?= $invalid('studentId') ?>" value="<?= h($form['studentId']) ?>" placeholder="0905-24-00000" required>
            <?= $message('studentId') ?>

            <label for="studentName" class="form-label small text-secondary">Nombre completo *</label>
            <input type="text" id="studentName" name="studentName" class="form-control <?= $invalid('studentName') ?>" value="<?= h($form['studentName']) ?>" maxlength="80" required>
            <?= $message('studentName') ?>
        </div>

        <!-- Paso 3 -->
        <div class="col-lg-4">
            <h2 class="h6 mb-3"><span class="badge rounded-circle text-bg-primary me-2">3</span>¿Hasta cuándo?</h2>
            <label for="loanDate" class="form-label small text-secondary">Fecha del préstamo *</label>
            <input type="date" id="loanDate" name="loanDate" class="form-control mb-3 <?= $invalid('loanDate') ?>" value="<?= h($form['loanDate']) ?>" required>
            <?= $message('loanDate') ?>

            <label for="dueDate" class="form-label small text-secondary">Fecha prevista de devolución *</label>
            <input type="date" id="dueDate" name="dueDate" class="form-control <?= $invalid('dueDate') ?>" value="<?= h($form['dueDate']) ?>" required>
            <?= $message('dueDate') ?>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-top mt-4 pt-3">
        <span class="small text-secondary"><i class="fa-regular fa-bookmark me-1"></i>El préstamo se guarda con estado <strong>Activo</strong>.</span>
        <button class="btn btn-primary px-4"><i class="fa-solid fa-check me-2"></i>Confirmar préstamo</button>
    </div>
</form>

<h2 class="h4 mb-3">Libros fuera de la biblioteca <span class="text-secondary fs-6">(<?= count($openLoans) ?>)</span></h2>

<?php if (!$openLoans): ?>
    <p class="text-secondary">No hay préstamos pendientes en este momento.</p>
<?php else: ?>
    <div class="table-responsive bg-white border rounded-3 shadow-sm">
        <table class="table table-borderless table-striped align-middle mb-0">
            <thead class="border-bottom">
                <tr class="small text-secondary">
                    <th class="ps-3">Préstamo</th><th>Libro</th><th>Estudiante</th><th>Prestado</th><th>Vence</th><th class="pe-3">Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($openLoans as $loan): ?>
                <tr>
                    <td class="ps-3 fw-semibold text-nowrap"><?= h($loan->code) ?></td>
                    <td class="text-primary fw-semibold text-nowrap"><?= h($loan->bookCode) ?></td>
                    <td><?= h($loan->studentName) ?> <small class="text-secondary d-block"><?= h($loan->studentId) ?></small></td>
                    <td class="text-nowrap"><?= nice_date($loan->loanDate) ?></td>
                    <td class="text-nowrap"><?= nice_date($loan->dueDate) ?></td>
                    <td class="pe-3"><?php include __DIR__ . '/partials/status_badge.php'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/bottom.php'; ?>
