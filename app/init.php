<?php
/* ============================================================
 * init.php — Punto de arranque
 * Todas las páginas lo cargan primero. Aquí se configura la
 * zona horaria, la sesión, las rutas y se cargan las clases.
 * ============================================================ */

date_default_timezone_set('America/Guatemala');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Rutas principales del proyecto
const ROOT_DIR    = __DIR__ . '/..';
const STORAGE_DIR = ROOT_DIR . '/storage';

// Datos generales que se muestran en la interfaz
const LIBRARY_NAME   = 'Biblioteca Miskatonic';
const LIBRARY_MOTTO  = 'Sistema de préstamos';

// Listas fijas para los formularios
const BOOK_GENRES = [
    'Novela', 'Cuento', 'Poesía', 'Historia', 'Ciencias',
    'Ingeniería', 'Programación', 'Filosofía', 'Arte', 'Consulta',
];
const BOOK_STATES = ['En circulación', 'Fuera de circulación'];

// Clases y funciones de la aplicación
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Flash.php';
require_once __DIR__ . '/TextStore.php';
require_once __DIR__ . '/FormValidator.php';
require_once __DIR__ . '/Book.php';
require_once __DIR__ . '/Loan.php';
require_once __DIR__ . '/BookRepository.php';
require_once __DIR__ . '/LoanRepository.php';
require_once __DIR__ . '/LibraryService.php';

// Objetos compartidos por todas las páginas
$books   = new BookRepository(new TextStore(STORAGE_DIR . '/books.txt', Book::FIELDS));
$loans   = new LoanRepository(new TextStore(STORAGE_DIR . '/loans.txt', Loan::FIELDS));
$library = new LibraryService($books, $loans);
