<?php
declare(strict_types=1);

namespace App\Contracts;

/** Kontrak yang dipakai oleh layanan peminjaman untuk polymorphism. */
interface Borrowable
{
    public function getLoanPeriodDays(): int;
}
