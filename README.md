# BibliotecaFase1

Sistema web para la **Biblioteca Universidad Miskatonic**: catálogo de libros, préstamos, devoluciones y consultas.
Hecho con **PHP 8.1+** y **Bootstrap 5**. Los datos se guardan en archivos `.txt` (sin base de datos).

## Ejecutar

```bash
php -S localhost:8080
```

y abrir <http://localhost:8080>.

## Organización

| Carpeta / archivo | Contenido |
|---|---|
| `index.php`, `books.php`, `book.php`, `book_delete.php`, `loans.php`, `returns.php`, `reports.php` | Páginas |
| `app/` | Clases: modelos (`Book`, `Loan`), repositorios, validación y reglas del negocio (`LibraryService`) |
| `partials/` | Piezas reutilizables: encabezado (`header.php`), menú (`sidebar.php`), pie de página (`footer.php`), avisos (`notices.php`), etiqueta de estado (`status_badge.php`) y los contenedores `top.php` / `bottom.php` |
| `storage/` | Datos: `books.txt` y `loans.txt` |
| `public/` | CSS, JavaScript y librerías locales (Bootstrap y Font Awesome) |

## Formato de los datos

Una línea por registro, campos separados por `;`:

```
books.txt  -> código;título;autor;categoría;año;ejemplares;disponibles;estado
loans.txt  -> código;libro;carné;nombre;fecha préstamo;fecha límite;fecha devolución;estado
```

## Reglas principales

- Solo se presta un libro que existe, está en circulación y tiene ejemplares disponibles.
- Prestar resta un ejemplar disponible y devolver lo suma.
- Un préstamo devuelto no puede devolverse otra vez.
- No se elimina un libro con préstamos sin devolver.
- Un préstamo es **Vencido** cuando sigue activo y ya pasó su fecha de devolución.
