<?php
/* ============================================================
 * partials/sidebar.php — Menú de navegación lateral
 * En pantallas grandes queda fijo a la izquierda; en celular
 * se convierte en un panel deslizable (offcanvas de Bootstrap).
 * ============================================================ */
$menu = [
    'home'    => ['index.php',   'fa-regular fa-compass',      'Inicio'],
    'books'   => ['books.php',   'fa-solid fa-book',           'Libros'],
    'loans'   => ['loans.php',   'fa-regular fa-handshake',     'Préstamos'],
    'returns' => ['returns.php', 'fa-solid fa-arrow-rotate-left',  'Devoluciones'],
    'reports' => ['reports.php', 'fa-regular fa-rectangle-list',   'Consultas'],
];
?>
<aside id="sidebar" class="sidebar offcanvas-lg offcanvas-start bg-white border-end" tabindex="-1" aria-label="Menú principal">

    <!-- Nombre de la biblioteca dentro del menú -->
    <div class="px-4 pt-4 pb-3 border-bottom d-flex align-items-start justify-content-between">
        <a href="index.php" class="text-decoration-none">
            <span class="d-block text-primary fs-3 mb-1"><i class="fa-solid fa-book-open-reader"></i></span>
            <span class="d-block font-serif fs-5 fw-semibold text-body lh-sm"><?= LIBRARY_NAME ?></span>
            <span class="d-block small text-secondary"><?= LIBRARY_MOTTO ?></span>
        </a>
        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Cerrar menú"></button>
    </div>

    <nav class="p-3">
        <p class="text-uppercase small text-secondary fw-semibold px-3 mb-2">Menú</p>
        <ul class="nav flex-column gap-1">
            <?php foreach ($menu as $key => [$url, $icon, $label]): ?>
                <li class="nav-item">
                    <a href="<?= $url ?>"
                       class="nav-link d-flex align-items-center gap-3 rounded-2 <?= $section === $key ? 'active' : '' ?>"
                       <?= $section === $key ? 'aria-current="page"' : '' ?>>
                        <i class="<?= $icon ?> fa-fw"></i><?= $label ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

</aside>
