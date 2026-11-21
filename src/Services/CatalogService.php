<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BookFactory;
use App\Repositories\JsonStore;
use App\Support\Validation;
use DomainException;

final class CatalogService
{
    public const CATEGORIES = ['Teknologi', 'Fiksi', 'Bisnis', 'Desain', 'Pengembangan Diri'];
    public const ACCENTS = ['blue', 'mint', 'orange', 'sand', 'purple', 'rose', 'indigo', 'teal'];

    public function __construct(private JsonStore $store) {}

    public function save(array $input): void
    {
        $id = Validation::optionalText($input, 'id', 'ID buku', 40);
        $title = Validation::text($input, 'title', 'Judul buku', 2, 100);
        $author = Validation::text($input, 'author', 'Nama penulis', 2, 100);
        $category = Validation::oneOf($input, 'category', 'Kategori', self::CATEGORIES);
        $year = Validation::year($input);
        $description = Validation::optionalText($input, 'description', 'Deskripsi', 300);
        $accent = Validation::oneOf($input, 'accent', 'Warna sampul', self::ACCENTS);
        $kind = Validation::oneOf($input, 'kind', 'Jenis e-book', ['standard', 'premium']);
        $this->store->transaction(static function (array &$state) use ($id, $title, $author, $category, $year, $description, $accent, $kind): void {
            $index = null;
            foreach ($state['books'] as $i => $item) {
                if ($item['id'] === $id) { $index = $i; break; }
            }
            if ($id !== '' && $index === null) {
                throw new DomainException('Buku yang ingin diedit tidak ditemukan.');
            }
            $newId = $id !== '' ? $id : 'BK' . strtoupper(bin2hex(random_bytes(5)));
            $book = BookFactory::fromArray(compact('title', 'author', 'category', 'year', 'description', 'accent', 'kind') + ['id' => $newId]);
            if ($index === null) {
                $state['books'][] = $book->toArray();
            } else {
                $state['books'][$index] = $book->toArray();
            }
        });
    }

    public function delete(array $input): void
    {
        $id = Validation::text($input, 'id', 'ID buku', 2, 40);
        $this->store->transaction(static function (array &$state) use ($id): void {
            foreach ($state['loans'] as $loan) {
                if ($loan['book_id'] === $id && $loan['returned_at'] === null) {
                    throw new DomainException('Buku masih dipinjam. Kembalikan dahulu sebelum dihapus.');
                }
            }
            $initial = count($state['books']);
            $state['books'] = array_values(array_filter($state['books'], static fn(array $book): bool => $book['id'] !== $id));
            if (count($state['books']) === $initial) {
                throw new DomainException('Buku tidak ditemukan.');
            }
        });
    }
}
