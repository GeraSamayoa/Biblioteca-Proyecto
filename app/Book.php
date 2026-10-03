<?php

class Book
{
    // Orden de las columnas dentro de storage/books.txt
    public const FIELDS = ['code', 'title', 'author', 'genre', 'year', 'copies', 'available', 'state'];

    public string $code = '';
    public string $title = '';
    public string $author = '';
    public string $genre = '';
    public int $year = 0;
    public int $copies = 0;     // ejemplares totales
    public int $available = 0;  // ejemplares en estante (no prestados)
    public string $state = 'En circulación';

    /** Crea un libro a partir de una fila del archivo o de un formulario. */
    public static function fromArray(array $data): self
    {
        $book = new self();
        $book->code      = strtoupper(trim($data['code'] ?? ''));
        $book->title     = trim($data['title'] ?? '');
        $book->author    = trim($data['author'] ?? '');
        $book->genre     = trim($data['genre'] ?? '');
        $book->year      = (int) ($data['year'] ?? 0);
        $book->copies    = (int) ($data['copies'] ?? 0);
        $book->available = (int) ($data['available'] ?? $book->copies);
        $book->state     = trim($data['state'] ?? 'En circulación');
        return $book;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function isLendable(): bool
    {
        return $this->state === 'En circulación' && $this->available > 0;
    }

    // Cantidad de ejemplares que están en manos de estudiantes
    public function onLoan(): int
    {
        return $this->copies - $this->available;
    }
}
