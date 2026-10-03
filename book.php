<?php
/* ============================================================
 * book.php — Alta y edición de un libro
 *   book.php            -> registrar un libro nuevo
 *   book.php?code=XXX   -> editar el libro XXX
 * ============================================================ */
require __DIR__ . '/app/init.php';

$editCode = param('code');
$editing  = $editCode !== '';
$original = $editing ? $books->find($editCode) : null;

if ($editing && !$original) {
    Flash::error('No se encontró el libro que querías editar.');
    go('books.php');
}

// Valores que se muestran en el formulario
$form = $original ? $original->toArray() : [
    'code' => '', 'title' => '', 'author' => '', 'genre' => '',
    'year' => '', 'copies' => '1', 'state' => 'En circulación',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['code', 'title', 'author', 'genre', 'year', 'copies', 'state'] as $name) {
        $form[$name] = field($name);
    }

    $errors = $editing
        ? $library->updateBook($original->code, $form)
        : $library->addBook($form);

    if (!$errors) {
        Flash::ok($editing ? 'Los cambios del libro se guardaron correctamente.' : 'El libro se registró correctamente.');
        go('books.php');
    }
}

// Pequeña ayuda para no repetir la lógica de errores en cada campo
$invalid = fn(string $name) => isset($errors[$name]) ? 'is-invalid' : '';
$message = fn(string $name) => '<div class="invalid-feedback">' . h($errors[$name] ?? '') . '</div>';

$pageTitle = $editing ? 'Editar libro' : 'Nuevo libro';
$section   = 'books';
include __DIR__ . '/partials/top.php';
?>

<nav aria-label="Ruta" class="mb-2">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="books.php">Libros</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $editing ? h($original->code) : 'Nuevo' ?></li>
    </ol>
</nav>
<h1 class="h2 mb-4"><?= $editing ? 'Editar «' . h($original->title) . '»' : 'Agregar un libro al catálogo' ?></h1>

<div class="row g-4">
    <div class="col-xl-8">
        <form method="post" class="bg-white border rounded-3 shadow-sm p-4" novalidate>
            <?php if ($errors): ?>
                <div class="alert alert-danger py-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Hay datos por corregir.</div>
            <?php endif; ?>

            <h2 class="h6 text-uppercase text-secondary mb-3">Identificación</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="form-floating">
                    <input type="text" id="code" name="code" class="form-control <?= $invalid('code') ?>" placeholder="MSK-1001"
                           value="<?= h($form['code']) ?>" maxlength="10" <?= $editing ? 'readonly' : 'required' ?>>
                    <label for="code">Código *</label>
                    <?= $message('code') ?>
                </div></div>
                <div class="col-md-8"><div class="form-floating">
                    <input type="text" id="title" name="title" class="form-control <?= $invalid('title') ?>" placeholder="Título"
                           value="<?= h($form['title']) ?>" maxlength="150" required>
                    <label for="title">Título *</label>
                    <?= $message('title') ?>
                </div></div>
                <div class="col-md-7"><div class="form-floating">
                    <input type="text" id="author" name="author" class="form-control <?= $invalid('author') ?>" placeholder="Autor"
                           value="<?= h($form['author']) ?>" maxlength="100" required>
                    <label for="author">Autor *</label>
                    <?= $message('author') ?>
                </div></div>
                <div class="col-md-5"><div class="form-floating">
                    <select id="genre" name="genre" class="form-select <?= $invalid('genre') ?>" required>
                        <option value="">—</option>
                        <?php foreach (BOOK_GENRES as $g): ?>
                            <option <?= $form['genre'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="genre">Categoría *</label>
                    <?= $message('genre') ?>
                </div></div>
            </div>

            <h2 class="h6 text-uppercase text-secondary mb-3">Existencias</h2>
            <div class="row g-3">
                <div class="col-md-4"><div class="form-floating">
                    <input type="number" id="year" name="year" class="form-control <?= $invalid('year') ?>" placeholder="Año"
                           value="<?= h($form['year']) ?>" min="1450" max="<?= date('Y') ?>" required>
                    <label for="year">Año de publicación *</label>
                    <?= $message('year') ?>
                </div></div>
                <div class="col-md-4"><div class="form-floating">
                    <input type="number" id="copies" name="copies" class="form-control <?= $invalid('copies') ?>" placeholder="1"
                           value="<?= h($form['copies']) ?>" min="1" max="500" required>
                    <label for="copies">Cantidad de ejemplares *</label>
                    <?= $message('copies') ?>
                </div></div>
                <div class="col-md-4"><div class="form-floating">
                    <select id="state" name="state" class="form-select <?= $invalid('state') ?>" required>
                        <?php foreach (BOOK_STATES as $s): ?>
                            <option <?= $form['state'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="state">Estado *</label>
                    <?= $message('state') ?>
                </div></div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="books.php" class="btn btn-link text-secondary text-decoration-none">Volver sin guardar</a>
                <button class="btn btn-primary px-4"><i class="fa-regular fa-floppy-disk me-2"></i>Guardar</button>
            </div>
        </form>
    </div>

    <!-- Columna de ayuda -->
    <aside class="col-xl-4">
        <div class="bg-primary-subtle rounded-3 p-4 small">
            <h2 class="h6 font-serif"><i class="fa-regular fa-lightbulb me-2"></i>Antes de guardar</h2>
            <ul class="mb-0 ps-3">
                <li>El código lleva letras, un guion y números: <strong>MSK-1001</strong>.</li>
                <li>Los campos con * son obligatorios.</li>
                <li>Un libro <em>fuera de circulación</em> no se puede prestar.</li>
                <?php if ($editing): ?>
                    <li>Prestados en este momento: <strong><?= $original->onLoan() ?></strong>. No puedes dejar menos ejemplares que esa cantidad.</li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>
</div>

<?php include __DIR__ . '/partials/bottom.php'; ?>
