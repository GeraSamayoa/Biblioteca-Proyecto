<?php

class FormValidator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    private function value(string $key): string
    {
        return trim((string) ($this->data[$key] ?? ''));
    }

    private function fail(string $key, string $message): self
    {
        $this->errors[$key] ??= $message;
        return $this;
    }

    public function required(string $key, string $label): self
    {
        return $this->value($key) === '' ? $this->fail($key, "$label es obligatorio.") : $this;
    }

    public function matches(string $key, string $regex, string $message): self
    {
        $v = $this->value($key);
        return ($v !== '' && !preg_match($regex, $v)) ? $this->fail($key, $message) : $this;
    }

    public function integerBetween(string $key, int $min, int $max, string $label): self
    {
        $v = $this->value($key);
        if ($v !== '' && (!ctype_digit($v) || (int) $v < $min || (int) $v > $max)) {
            $this->fail($key, "$label debe ser un número entero entre $min y $max.");
        }
        return $this;
    }

    public function maxLength(string $key, int $max, string $label): self
    {
        $length = function_exists('mb_strlen') ? mb_strlen($this->value($key)) : strlen($this->value($key));
        return $length > $max ? $this->fail($key, "$label admite como máximo $max caracteres.") : $this;
    }

    public function oneOf(string $key, array $options, string $label): self
    {
        $v = $this->value($key);
        return ($v !== '' && !in_array($v, $options, true)) ? $this->fail($key, "Elige una opción válida en $label.") : $this;
    }

    public function date(string $key, string $label): self
    {
        $v = $this->value($key);
        $d = DateTime::createFromFormat('Y-m-d', $v);
        return ($v !== '' && (!$d || $d->format('Y-m-d') !== $v)) ? $this->fail($key, "$label no es una fecha válida.") : $this;
    }

    // Para reglas que dependen de otros datos (por ejemplo, que el libro exista)
    public function check(bool $condition, string $key, string $message): self
    {
        return $condition ? $this : $this->fail($key, $message);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
