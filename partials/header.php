<?php
/* ============================================================
 * partials/header.php — Encabezado común de todas las páginas
 * Barra superior del área de contenido: muestra en qué sección
 * está el usuario, la fecha de hoy y un acceso rápido.
 * En celular incluye además el botón que abre el menú lateral.
 * ============================================================ */
?>
<header class="bg-white border-bottom px-3 px-md-4 px-xl-5 py-3 d-flex align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-primary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Abrir menú">
            <i class="fa-solid fa-bars"></i>
        </button>
        <span class="d-sm-none small fw-semibold"><?= h($pageTitle) ?></span>
        <nav aria-label="Ubicación" class="d-none d-sm-block">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none"><?= LIBRARY_NAME ?></a></li>
                <li class="breadcrumb-item active fw-semibold" aria-current="page"><?= h($pageTitle) ?></li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-3">
        <span class="small text-secondary d-none d-md-inline"><i class="fa-regular fa-calendar me-1"></i><?= nice_date(today()) ?></span>
        <a href="loans.php" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Préstamo</a>
    </div>
</header>
