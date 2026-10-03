<?php
/* ============================================================
 * partials/top.php — Inicio de cada página
 * Contiene el <head>, la barra lateral, el encabezado y abre el área de contenido.
 *
 * Antes de incluirlo, la página define:
 *   $pageTitle  -> texto de la pestaña del navegador
 *   $section    -> clave del menú que se marca como activa
 * ============================================================ */
$pageTitle ??= LIBRARY_NAME;
$section   ??= '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> — <?= LIBRARY_NAME ?></title>

    <!-- Fuentes: Lora para títulos, Source Sans 3 para el texto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Source+Sans+3:wght@400;600;700&display=swap">

    <!-- Librerías (copias locales dentro de public/lib) -->
    <link rel="stylesheet" href="public/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/lib/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="public/lib/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="public/lib/fontawesome/css/regular.min.css">

    <!-- Tema café -->
    <link rel="stylesheet" href="public/css/theme.css">
</head>
<body>

<div class="d-lg-flex">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="flex-grow-1 d-flex flex-column min-vh-100">

        <?php include __DIR__ . '/header.php'; ?>

        <main class="flex-grow-1 px-3 px-md-4 px-xl-5 py-4">
            <?php include __DIR__ . '/notices.php'; ?>
