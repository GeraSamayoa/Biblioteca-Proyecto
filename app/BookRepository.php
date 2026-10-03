<?php

class BookRepository
{
    public function __construct(private TextStore $store)
    {
    }

    /** @return Book[] */
    public function all(): array
    {
        return array_map([Book::class, 'fromArray'], $this->store->readAll());
    }

    public function find(string $code): ?Book
    {
        foreach ($this->all() as $book) {
            if (strcasecmp($book->code, $code) === 0) {
                return $book;
            }
        }
        return null;
    }

    public function exists(string $code): bool
    {
        return $this->find($code) !== null;
    }

    /**
     * Búsqueda por uno o varios criterios.
     * $term se compara contra el campo elegido en $by (code, title, author o "all").
     * $genre filtra por categoría exacta.
     */
    public function search(string $term, string $by = 'all', string $genre = ''): array
    {
        $columns = $by === 'all' ? ['code', 'title', 'author', 'genre'] : [$by];

        return array_values(array_filter($this->all(), function (Book $b) use ($term, $columns, $genre) {
            if ($genre !== '' && $b->genre !== $genre) {
                return false;
            }
            if ($term === '') {
                return true;
            }
            foreach ($columns as $col) {
                if (contains((string) $b->$col, $term)) {
                    return true;
                }
            }
            return false;
        }));
    }

    public function save(Book $book): void
    {
        $list = $this->all();
        $replaced = false;

        foreach ($list as $i => $existing) {
            if ($existing->code === $book->code) {
                $list[$i] = $book; // ya existía: se actualiza
                $replaced = true;
            }
        }
        if (!$replaced) {
            $list[] = $book;   // nuevo: se agrega al final
        }
        $this->persist($list);
    }

    public function delete(string $code): void
    {
        $list = array_filter($this->all(), fn(Book $b) => strcasecmp($b->code, $code) !== 0);
        $this->persist($list);
    }

    private function persist(array $books): void
    {
        $this->store->writeAll(array_map(fn(Book $b) => $b->toArray(), $books));
    }
}
