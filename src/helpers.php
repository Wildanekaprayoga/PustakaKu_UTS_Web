<?php
declare(strict_types=1);

/** Escape seluruh teks dinamis sebelum dimasukkan ke HTML/atribut. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Tampilkan tanggal sesuai bahasa Indonesia tanpa ekstensi tambahan. */
function tanggal(?string $date): string
{
    if (!$date) {
        return '—';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '—';
    }
    $months = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}
