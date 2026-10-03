<?php
/* ============================================================
 * Loan.php — Modelo de un préstamo
 *
 * En el archivo solo se guardan dos estados: Activo y Devuelto.
 * "Vencido" no se guarda: se calcula comparando la fecha
 * límite con la fecha de hoy (ver status()).
 * ============================================================ */

class Loan
{
    public const FIELDS = ['code', 'bookCode', 'studentId', 'studentName', 'loanDate', 'dueDate', 'returnDate', 'state'];

    public const ACTIVE   = 'Activo';
    public const RETURNED = 'Devuelto';
    public const OVERDUE  = 'Vencido';

    public string $code = '';
    public string $bookCode = '';
    public string $studentId = '';     // carné
    public string $studentName = '';
    public string $loanDate = '';
    public string $dueDate = '';       // fecha prevista de devolución
    public string $returnDate = '';    // se llena al devolver
    public string $state = self::ACTIVE;

    public static function fromArray(array $data): self
    {
        $loan = new self();
        foreach (self::FIELDS as $f) {
            $loan->$f = trim((string) ($data[$f] ?? ''));
        }
        $loan->code     = strtoupper($loan->code);
        $loan->bookCode = strtoupper($loan->bookCode);
        $loan->state    = $loan->state ?: self::ACTIVE;
        return $loan;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /** Estado real que ve el usuario: Activo, Vencido o Devuelto. */
    public function status(): string
    {
        if ($this->state === self::RETURNED) {
            return self::RETURNED;
        }
        return $this->dueDate < today() ? self::OVERDUE : self::ACTIVE;
    }

    public function isOpen(): bool
    {
        return $this->state !== self::RETURNED;
    }

    // Días de atraso (0 si no está vencido)
    public function daysLate(): int
    {
        if ($this->status() !== self::OVERDUE) {
            return 0;
        }
        return (int) (new DateTime($this->dueDate))->diff(new DateTime(today()))->days;
    }
}
