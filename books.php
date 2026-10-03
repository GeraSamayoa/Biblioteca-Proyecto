<?php
/* ============================================================
 * books.php — Catálogo de libros
 * Lista todos los libros y permite buscarlos por código,
 * título, autor o categoría.
 * ============================================================ */
require __DIR__ . '/app/init.php';

// Criterios de búsqueda que llegan por la URL
$q     = param('q');
$by    = in_array(param('by'), ['all', 'code', 'title', 'author'], true) ? param('by') : 'all';
$genre = in_array(param('genre'), BOOK_GENRES, true) ? param('genre') : '';

$list      = $books->search($q, $by, $genre);
$filtering = $q !== '' || $genre !== '';

$searchFields = ['all' => 'Todo', 'code' => 'Código', 'title' => 'Título', 'author' => 'Autor'];

$pageTitle = 'Libros';
$section   = 'books';
include __DIR__ . '/partials/top.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="text-uppercase small fw-semibold text-primary mb-1">Catálogo</p>
        <h1 class="h2 mb-0">Libros de la colección</h1>
    </div>
    <a href="book.php" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Nuevo libro</a>
</div>

<!-- Buscador: un texto + en qué campo buscar + categoría -->
<form method="get" class="row g-2 mb-4" role="search">
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
            <input type="search" name="q" value="<?= h($q) ?>" class="form-control" placeholder="Escribe para buscar…" aria-label="Texto a buscar">
        </div>
    </div>
    <div class="col-6 col-md-2">
        <select name="by" class="form-select" aria-label="Buscar en">
            <?php foreach ($searchFields as $value => $label): ?>
                <option value="<?= $value ?>" <?= $by === $value ? 'selected' : '' ?>>En: <?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-3">
        <select name="genre" class="form-select" aria-label="Categoría">
            <option value="">Todas las categorías</option>
            <?php foreach (BOOK_GENRES as $g): ?>
                <option <?= $genre === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-outline-primary">Buscar</button>
    </div>
</form>

<?php if ($filtering): ?>
    <p class="small text-secondary">
        <?= count($list) ?> resultado(s) para tu búsqueda · <a href="books.php">quitar filtros</a>
    </p>
<?php endif; ?>

<?php if (!$list): ?>
    <div class="text-center bg-white border rounded-3 py-5 text-secondary">
        <i class="fa-regular fa-folder-open fs-2 d-block mb-2"></i>
        <?= $filtering ? 'Ningún libro coincide con la búsqueda.' : 'El catálogo está vacío. Registra el primer libro.' ?>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white border rounded-3 shadow-sm">
        <table class="table table-borderless table-striped align-middle mb-0">
            <thead class="border-bottom">
                <tr class="small text-secondary">
                    <th class="ps-3">Código</th>
                    <th>Título y autor</th>
                    <th>Categoría</th>
                    <th>Año</th>
                    <th>Ejemplares</th>
                    <th>Estado</th>
                    <th class="text-end pe-3">Opciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $book): ?>
                <tr>
                    <td class="ps-3 text-nowrap fw-semibold text-primary"><?= h($book->code) ?></td>
                    <td>
                        <div class="fw-semibold"><?= h($book->title) ?></div>
                        <small class="text-secondary"><?= h($book->author) ?></small>
                    </td>
                    <td><?= h($book->genre) ?></td>
                    <td><?= $book->year ?></td>
                    <td class="text-nowrap">
                        <span class="<?= $book->available ? 'text-success' : 'text-danger' ?> fw-semibold"><?= $book->available ?></span>
                        <span class="text-secondary">de <?= $book->copies ?></span>
                    </td>
                    <td>
                        <?php if ($book->state === 'En circulación'): ?>
                            <span class="small"><i class="fa-solid fa-circle text-success fa-2xs me-1"></i>En circulación</span>
                        <?php else: ?>
                            <span class="small text-secondary"><i class="fa-regular fa-circle fa-2xs me-1"></i>Fuera de circulación</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3 text-nowrap">
                        <a href="book.php?code=<?= urlencode($book->code) ?>" class="btn btn-sm btn-link text-decoration-none" title="Editar">
                            <i class="fa-regular fa-pen-to-square"></i> Editar
                        </a>
                        <a href="book_delete.php?code=<?= urlencode($book->code) ?>" class="btn btn-sm btn-link text-danger text-decoration-none" title="Eliminar">
                            <i class="fa-regular fa-trash-can"></i> Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/bottom.php'; ?>
