<?php

class LibraryService
{
    public function __construct(
        private BookRepository $books,
        private LoanRepository $loans
    ) {
    }

    /* ---------- Libros ---------- */

    private function bookRules(array $input, bool $isNew): FormValidator
    {
        $maxYear = (int) date('Y');

        $v = (new FormValidator($input))
            ->required('code', 'El código')
            ->required('title', 'El título')
            ->required('author', 'El autor')
            ->required('genre', 'La categoría')
            ->required('year', 'El año de publicación')
            ->required('copies', 'La cantidad de ejemplares')
            ->required('state', 'El estado')
            ->matches('code', '/^[A-Za-z]{2,4}-\d{2,5}$/', 'Usa el formato LETRAS-NÚMEROS, por ejemplo MSK-1001.')
            ->maxLength('title', 150, 'El título')
            ->maxLength('author', 100, 'El autor')
            ->oneOf('genre', BOOK_GENRES, 'categoría')
            ->oneOf('state', BOOK_STATES, 'estado')
            ->integerBetween('year', 1450, $maxYear, 'El año')
            ->integerBetween('copies', 1, 500, 'La cantidad de ejemplares');

        if ($isNew && trim($input['code'] ?? '') !== '') {
            $v->check(!$this->books->exists($input['code']), 'code', 'Ese código ya pertenece a otro libro.');
        }
        return $v;
    }

    public function addBook(array $input): array
    {
        $v = $this->bookRules($input, true);
        if ($v->fails()) {
            return $v->errors();
        }

        $book = Book::fromArray($input);
        $book->available = $book->copies; // al inicio todos están en estante
        $this->books->save($book);
        return [];
    }

    public function updateBook(string $code, array $input): array
    {
        $current = $this->books->find($code);
        if (!$current) {
            return ['code' => 'El libro ya no existe.'];
        }

        $input['code'] = $current->code; // el código no cambia
        $v = $this->bookRules($input, false);

        $lent = $this->loans->openCountFor($current->code);
        $v->check((int) ($input['copies'] ?? 0) >= $lent, 'copies',
            "Hay $lent ejemplar(es) prestado(s); no puedes registrar menos que eso.");

        if ($v->fails()) {
            return $v->errors();
        }

        $book = Book::fromArray($input);
        $book->available = $book->copies - $lent;
        $this->books->save($book);
        return [];
    }

    public function removeBook(string $code): ?string
    {
        if (!$this->books->exists($code)) {
            return 'El libro ya no existe.';
        }
        if ($this->loans->openCountFor($code) > 0) {
            return 'El libro no puede eliminarse porque tiene un préstamo activo.';
        }
        $this->books->delete($code);
        return null;
    }

    /* ---------- Préstamos ---------- */

    public function lend(array $input): array
    {
        $v = (new FormValidator($input))
            ->required('code', 'El código del préstamo')
            ->required('bookCode', 'El código del libro')
            ->required('studentId', 'El carné')
            ->required('studentName', 'El nombre')
            ->required('loanDate', 'La fecha del préstamo')
            ->required('dueDate', 'La fecha de devolución')
            ->matches('code', '/^[A-Za-z]{2,4}-\d{2,6}$/', 'Usa el formato PR-0001.')
            ->matches('studentId', '/^\d{4}-\d{2}-\d{3,6}$/', 'El carné debe tener el formato 0000-00-00000.')
            ->matches('studentName', '/^[\p{L} .\'-]{3,80}$/u', 'Escribe un nombre válido (solo letras).')
            ->date('loanDate', 'La fecha del préstamo')
            ->date('dueDate', 'La fecha de devolución');

        $errors = $v->errors();

        if (!isset($errors['code']) && $this->loans->find($input['code'])) {
            $errors['code'] = 'Ese código de préstamo ya se usó.';
        }
        if (!isset($errors['loanDate']) && !isset($errors['dueDate']) && ($input['dueDate'] ?? '') < ($input['loanDate'] ?? '')) {
            $errors['dueDate'] ??= 'La devolución no puede ser antes del préstamo.';
        }

        // El libro debe existir y tener al menos un ejemplar en estante
        if (!isset($errors['bookCode'])) {
            $book = $this->books->find($input['bookCode']);
            if (!$book) {
                $errors['bookCode'] = 'No existe ningún libro con ese código.';
            } elseif ($book->state !== 'En circulación') {
                $errors['bookCode'] = 'Ese libro está fuera de circulación.';
            } elseif ($book->available < 1) {
                $errors['bookCode'] = 'No existen ejemplares disponibles de este libro.';
            }
        }

        if ($errors) {
            return $errors;
        }

        $loan = Loan::fromArray($input);
        $loan->state = Loan::ACTIVE;
        $this->loans->save($loan);

        $book->available--;
        $this->books->save($book);
        return [];
    }

    public function receive(string $loanCode): ?string
    {
        $loan = $this->loans->find($loanCode);
        if (!$loan) {
            return 'No se encontró el préstamo ' . $loanCode . '.';
        }
        if (!$loan->isOpen()) {
            return 'Este préstamo ya había sido devuelto.';
        }

        $loan->state = Loan::RETURNED;
        $loan->returnDate = today();
        $this->loans->save($loan);

        // El ejemplar regresa al estante
        if ($book = $this->books->find($loan->bookCode)) {
            $book->available = min($book->copies, $book->available + 1);
            $this->books->save($book);
        }
        return null;
    }
}
