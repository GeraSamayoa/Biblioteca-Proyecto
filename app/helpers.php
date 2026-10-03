<?php
/* ============================================================
 * helpers.php — Funciones pequeñas de uso general
 * ============================================================ */

// Escapa un texto antes de imprimirlo en el HTML (evita inyección de código)
function h(mixed $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Lee un campo enviado por POST, ya sin espacios al inicio y al final
function field(string $name): string
{
    return trim((string) ($_POST[$name] ?? ''));
}

// Lee un parámetro de la URL (GET)
function param(string $name): string
{
    return trim((string) ($_GET[$name] ?? ''));
}

// Envía al usuario a otra página y termina el script
function go(string $page): never
{
    header("Location: $page");
    exit;
}

// 2026-10-02  ->  02 oct 2026
function nice_date(?string $isoDate): string
{
    if (!$isoDate) {
        return '—';
    }
    $months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    [$y, $m, $d] = explode('-', $isoDate);
    return sprintf('%s %s %s', $d, $months[(int) $m - 1], $y);
}

// Fecha de hoy en formato ISO (AAAA-MM-DD), que es como se guarda
function today(): string
{
    return date('Y-m-d');
}

// Comparación de texto sin importar mayúsculas ni tildes simples
function contains(string $haystack, string $needle): bool
{
    if (function_exists('mb_stripos')) {
        return mb_stripos($haystack, $needle) !== false;
    }
    return stripos($haystack, $needle) !== false;
}
