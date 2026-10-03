<?php

class Flash
{
    private const KEY = 'notices';

    // Tipos válidos: ok | error | info
    public static function push(string $type, string $message): void
    {
        $_SESSION[self::KEY][] = ['type' => $type, 'message' => $message];
    }

    public static function ok(string $message): void
    {
        self::push('ok', $message);
    }

    public static function error(string $message): void
    {
        self::push('error', $message);
    }

    // Devuelve todos los avisos pendientes y los borra
    public static function pullAll(): array
    {
        $notices = $_SESSION[self::KEY] ?? [];
        unset($_SESSION[self::KEY]);
        return $notices;
    }
}
