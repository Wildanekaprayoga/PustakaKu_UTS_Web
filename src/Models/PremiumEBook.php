<?php
declare(strict_types=1);

namespace App\Models;

/** Subclass meng-override method: polymorphism -> pinjam 14 hari. */
final class PremiumEBook extends EBook
{
    public function getKind(): string { return 'premium'; }
    public function getKindLabel(): string { return 'Premium'; }
    public function getLoanPeriodDays(): int { return 14; }
}
