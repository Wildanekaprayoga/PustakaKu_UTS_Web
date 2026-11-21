<?php
declare(strict_types=1);

namespace App\Repositories;

/** Data contoh otomatis hanya ketika database.json belum ada. */
final class SeedData
{
    public static function make(): array
    {
        $raw = [
            ['BK001', 'Pemrograman PHP Modern', 'Rizky Ramadhan', 'Teknologi', 2025, 'Pelajari PHP 8 dan fondasi aplikasi web modern.', 'blue', 'standard'],
            ['BK002', 'Clean Code', 'Robert C. Martin', 'Teknologi', 2008, 'Prinsip menulis kode yang mudah dibaca dan dirawat.', 'mint', 'premium'],
            ['BK003', 'Laskar Pelangi', 'Andrea Hirata', 'Fiksi', 2005, 'Kisah persahabatan dan perjuangan meraih mimpi.', 'orange', 'standard'],
            ['BK004', 'Atomic Habits', 'James Clear', 'Pengembangan Diri', 2018, 'Kebiasaan kecil yang menghasilkan perubahan besar.', 'sand', 'premium'],
            ['BK005', 'Filosofi Teras', 'Henry Manampiring', 'Pengembangan Diri', 2018, 'Mengenal filsafat Stoa dalam kehidupan sehari-hari.', 'purple', 'standard'],
            ['BK006', 'Dasar-Dasar UI/UX', 'Nadia Putri', 'Desain', 2024, 'Fundamental desain antarmuka dan pengalaman pengguna.', 'rose', 'standard'],
            ['BK007', 'Algoritma dan Struktur Data', 'Budi Raharjo', 'Teknologi', 2023, 'Memahami algoritma, kompleksitas, dan struktur data.', 'indigo', 'premium'],
            ['BK008', 'Bumi', 'Tere Liye', 'Fiksi', 2014, 'Petualangan fantasi dalam dunia paralel.', 'teal', 'standard'],
        ];
        $books = array_map(static fn(array $b): array => [
            'id' => $b[0], 'title' => $b[1], 'author' => $b[2],
            'category' => $b[3], 'year' => $b[4], 'description' => $b[5],
            'accent' => $b[6], 'kind' => $b[7],
        ], $raw);
        $today = new \DateTimeImmutable('today');
        $loanDate = $today->modify('-2 days')->format('Y-m-d');
        return [
            'meta' => ['schema_version' => 1, 'created_at' => date(DATE_ATOM)],
            'books' => $books,
            'members' => [
                ['id' => 'MB001', 'name' => 'Budi Santoso', 'email' => 'budi@example.com'],
                ['id' => 'MB002', 'name' => 'Siti Aminah', 'email' => 'siti@example.com'],
                ['id' => 'MB003', 'name' => 'Ahmad Rizki', 'email' => 'ahmad@example.com'],
            ],
            'loans' => [
                [
                    'id' => 'LN001', 'book_id' => 'BK002', 'member_id' => 'MB001',
                    'book_title' => 'Clean Code', 'member_name' => 'Budi Santoso',
                    'borrowed_at' => $loanDate,
                    'due_at' => $today->modify('+12 days')->format('Y-m-d'),
                    'returned_at' => null,
                ],
                [
                    'id' => 'LN002', 'book_id' => 'BK003', 'member_id' => 'MB002',
                    'book_title' => 'Laskar Pelangi', 'member_name' => 'Siti Aminah',
                    'borrowed_at' => $today->modify('-8 days')->format('Y-m-d'),
                    'due_at' => $today->modify('-1 day')->format('Y-m-d'),
                    'returned_at' => $today->modify('-3 days')->format('Y-m-d'),
                ],
            ],
        ];
    }
}
