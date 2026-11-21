<?php
declare(strict_types=1);

namespace App\Models;

/** Jenis e-book standar: 7 hari peminjaman. */
class EBook extends Book
{
    public function getKind(): string { return 'standard'; }
    public function getKindLabel(): string { return 'Reguler'; }
    public function getLoanPeriodDays(): int { return 7; }
}
