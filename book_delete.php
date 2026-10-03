<?php

require __DIR__ . '/app/init.php';

$code = param('code');
$book = $books->find($code);

if (!$book) {
    Flash::error('No se encontró el libro indicado.');
    go('books.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $problem = $library->removeBook($book->code);
    $problem ? Flash::error($problem) : Flash::ok('El libro «' . $book->title . '» se eliminó del catálogo.');
    go('books.php');
}

$blocked = $loans->openCountFor($book->code) > 0;

$pageTitle = 'Eliminar libro';
$section   = 'books';
include __DIR__ . '/partials/top.php';
?>

<div class="mx-auto py-lg-4 col-lg-7 col-xl-6">
    <div class="bg-white border rounded-3 shadow-sm p-4 p-md-5 text-center">
        <span class="d-inline-block text-danger fs-1 mb-3"><i class="fa-regular fa-trash-can"></i></span>
        <h1 class="h3">¿Quitar este libro del catálogo?</h1>
        <p class="text-secondary">Se borrará de forma permanente del archivo de libros.</p>

        <dl class="row text-start border rounded-3 bg-body p-3 mx-0 my-4 small">
            <dt class="col-4">Código</dt>     <dd class="col-8"><?= h($book->code) ?></dd>
            <dt class="col-4">Título</dt>     <dd class="col-8"><?= h($book->title) ?></dd>
            <dt class="col-4">Autor</dt>      <dd class="col-8"><?= h($book->author) ?></dd>
            <dt class="col-4">Ejemplares</dt> <dd class="col-8 mb-0"><?= $book->copies ?> (<?= $book->onLoan() ?> prestados)</dd>
        </dl>

        <?php if ($blocked): ?>
            <div class="alert alert-warning text-start small">
                <i class="fa-solid fa-lock me-2"></i>El libro no puede eliminarse porque tiene un préstamo activo.
                Registra primero la devolución.
            </div>
            <a href="books.php" class="btn btn-outline-primary">Regresar al catálogo</a>
        <?php else: ?>
            <form method="post" class="d-flex justify-content-center gap-2">
                <a href="books.php" class="btn btn-outline-primary">No, conservarlo</a>
                <button class="btn btn-danger"><i class="fa-regular fa-trash-can me-2"></i>Sí, eliminar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/bottom.php'; ?>
