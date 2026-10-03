<?php

class LoanRepository
{
    public function __construct(private TextStore $store)
    {
    }

    /** @return Loan[] — del más reciente al más antiguo */
    public function all(): array
    {
        $loans = array_map([Loan::class, 'fromArray'], $this->store->readAll());
        return array_reverse($loans);
    }

    public function find(string $code): ?Loan
    {
        foreach ($this->all() as $loan) {
            if (strcasecmp($loan->code, $code) === 0) {
                return $loan;
            }
        }
        return null;
    }

    /** Préstamos filtrados por estado (Activo / Vencido / Devuelto) y por texto. */
    public function filter(string $status = '', string $text = ''): array
    {
        return array_values(array_filter($this->all(), function (Loan $l) use ($status, $text) {
            if ($status !== '' && $l->status() !== $status) {
                return false;
            }
            if ($text !== '') {
                return contains($l->code, $text) || contains($l->bookCode, $text)
                    || contains($l->studentId, $text) || contains($l->studentName, $text);
            }
            return true;
        }));
    }

    /** Préstamos sin devolver (activos + vencidos). */
    public function open(): array
    {
        return array_values(array_filter($this->all(), fn(Loan $l) => $l->isOpen()));
    }

    public function openCountFor(string $bookCode): int
    {
        return count(array_filter($this->open(), fn(Loan $l) => strcasecmp($l->bookCode, $bookCode) === 0));
    }

    /** Sugerencia de código: PR-0001, PR-0002, ... */
    public function nextCode(): string
    {
        $highest = 0;
        foreach ($this->all() as $loan) {
            if (preg_match('/^PR-(\d+)$/', $loan->code, $m)) {
                $highest = max($highest, (int) $m[1]);
            }
        }
        return sprintf('PR-%04d', $highest + 1);
    }

    public function save(Loan $loan): void
    {
        // all() viene invertido; se regresa al orden original antes de guardar
        $list = array_reverse($this->all());
        $found = false;

        foreach ($list as $i => $existing) {
            if ($existing->code === $loan->code) {
                $list[$i] = $loan;
                $found = true;
            }
        }
        if (!$found) {
            $list[] = $loan;
        }
        $this->store->writeAll(array_map(fn(Loan $l) => $l->toArray(), $list));
    }
}
