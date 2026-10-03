<?php
/* ============================================================
 * TextStore.php — Persistencia en archivos de texto plano
 *
 * Cada registro ocupa una línea y sus valores van separados
 * por punto y coma, siempre en el mismo orden de $fields.
 *
 *   MSK-1001;Hombres de maíz;Miguel Ángel Asturias;Novela;1949;3;3;En circulación
 *
 * No se usa ningún motor de base de datos.
 * ============================================================ */

class TextStore
{
    private const SEP = ';';

    public function __construct(
        private string $file,
        private array $fields
    ) {
        // Si la carpeta o el archivo no existen, se crean vacíos
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0775, true);
        }
        if (!is_file($file)) {
            touch($file);
        }
    }

    /** Lee el archivo completo y devuelve una lista de arreglos asociativos. */
    public function readAll(): array
    {
        $rows = [];
        $handle = fopen($this->file, 'r');
        flock($handle, LOCK_SH);

        while (($line = fgets($handle)) !== false) {
            $line = trim($line, "\r\n");
            if ($line === '') {
                continue; // líneas vacías se ignoran
            }
            $values = array_pad(explode(self::SEP, $line), count($this->fields), '');
            $rows[] = array_combine($this->fields, array_slice($values, 0, count($this->fields)));
        }

        flock($handle, LOCK_UN);
        fclose($handle);
        return $rows;
    }

    /** Reescribe el archivo con todos los registros recibidos. */
    public function writeAll(array $rows): void
    {
        $lines = '';
        foreach ($rows as $row) {
            $values = array_map(fn($f) => $this->clean($row[$f] ?? ''), $this->fields);
            $lines .= implode(self::SEP, $values) . PHP_EOL;
        }

        // LOCK_EX: nadie más puede escribir mientras se guarda
        file_put_contents($this->file, $lines, LOCK_EX);
    }

    // Quita el separador y los saltos de línea para no romper el formato
    private function clean(mixed $value): string
    {
        return str_replace([self::SEP, "\r", "\n"], [',', ' ', ' '], (string) ($value ?? ''));
    }
}
