<?php
declare(strict_types=1);

namespace App\Models;

/** Menghasilkan object dengan tipe subclass sesuai data. */
final class BookFactory
{
    public static function fromArray(array $data): Book
    {
        $class = ($data['kind'] ?? 'standard') === 'premium' ? PremiumEBook::class : EBook::class;
        return new $class(
            (string) $data['id'], (string) $data['title'], (string) $data['author'],
            (string) $data['category'], (int) $data['year'],
            (string) ($data['description'] ?? ''), (string) ($data['accent'] ?? 'blue')
        );
    }
}
