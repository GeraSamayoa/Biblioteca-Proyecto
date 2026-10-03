// ============================================================
// main.js — Comportamiento del lado del cliente
// La lógica real (validar, guardar) se hace en PHP; aquí solo
// se muestran las notificaciones y se piden confirmaciones.
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

    // Mostrar las notificaciones (toasts) que vienen del servidor
    document.querySelectorAll('.toast').forEach((el) => {
        bootstrap.Toast.getOrCreateInstance(el).show();
    });

    // Cualquier formulario con data-ask pide confirmación antes de enviarse
    document.querySelectorAll('form[data-ask]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.ask)) {
                event.preventDefault();
            }
        });
    });
});
