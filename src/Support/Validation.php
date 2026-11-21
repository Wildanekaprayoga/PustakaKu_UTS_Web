<?php
declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class Validation
{
    /** Menerima hanya scalar, mencegah parameter berbentuk array. */
    public static function text(array $input, string $key, string $label, int $min = 1, int $max = 120): string
    {
        $raw = $input[$key] ?? '';
        if (!is_string($raw) && !is_numeric($raw)) {
            throw new InvalidArgumentException($label . ' tidak valid.');
        }
        $value = trim((string) $raw);
        $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
        if ($length < $min || $length > $max) {
            throw new InvalidArgumentException("{$label} harus {$min}–{$max} karakter.");
        }
        return $value;
    }

    public static function oneOf(array $input, string $key, string $label, array $allowed): string
    {
        $value = self::text($input, $key, $label, 1, 80);
        if (!in_array($value, $allowed, true)) {
            throw new InvalidArgumentException($label . ' tidak tersedia.');
        }
        return $value;
    }

    public static function year(array $input, string $key = 'year'): int
    {
        $raw = self::text($input, $key, 'Tahun terbit', 4, 4);
        $year = filter_var($raw, FILTER_VALIDATE_INT);
        if ($year === false || $year < 1900 || $year > (int) date('Y') + 1) {
            throw new InvalidArgumentException('Tahun terbit harus antara 1900 dan tahun depan.');
        }
        return $year;
    }

    public static function email(array $input, string $key = 'email'): string
    {
        $email = strtolower(self::text($input, $key, 'Email', 5, 180));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Format email tidak valid.');
        }
        return $email;
    }

    public static function optionalText(array $input, string $key, string $label, int $max = 300): string
    {
        return self::text($input, $key, $label, 0, $max);
    }
}
