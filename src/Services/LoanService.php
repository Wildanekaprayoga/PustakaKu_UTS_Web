<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BookFactory;
use App\Models\Loan;
use App\Models\Member;
use App\Repositories\JsonStore;
use App\Support\Validation;
use DomainException;

/** Business logic lintas entitas. Polymorphism dipakai saat menentukan lama pinjam. */
final class LoanService
{
    public function __construct(private JsonStore $store) {}

    public function borrow(array $input): void
    {
        $bookId = Validation::text($input, 'book_id', 'Buku', 2, 40);
        $memberId = Validation::text($input, 'member_id', 'Anggota', 2, 40);
        $this->store->transaction(static function (array &$state) use ($bookId, $memberId): void {
            $rawBook = null;
            $rawMember = null;
            foreach ($state['books'] as $book) {
                if ($book['id'] === $bookId) { $rawBook = $book; break; }
            }
            foreach ($state['members'] as $member) {
                if ($member['id'] === $memberId) { $rawMember = $member; break; }
            }
            if ($rawBook === null || $rawMember === null) {
                throw new DomainException('Buku atau anggota tidak ditemukan.');
            }
            $activeForMember = 0;
            foreach ($state['loans'] as $loan) {
                if ($loan['returned_at'] !== null) { continue; }
                if ($loan['book_id'] === $bookId) {
                    throw new DomainException('E-book ini sedang dipinjam oleh anggota lain.');
                }
                if ($loan['member_id'] === $memberId) { $activeForMember++; }
            }
            if ($activeForMember >= 3) {
                throw new DomainException('Anggota sudah mencapai batas 3 pinjaman aktif.');
            }
            // Tipe parent Book; dispatch method milik EBook (7 hari) atau PremiumEBook (14 hari).
            $book = BookFactory::fromArray($rawBook);
            $member = Member::fromArray($rawMember);
            $today = new \DateTimeImmutable('today');
            $loan = new Loan(
                'LN' . strtoupper(bin2hex(random_bytes(5))), $book->getId(), $member->getId(),
                $book->getTitle(), $member->getName(), $today->format('Y-m-d'),
                $today->modify('+' . $book->getLoanPeriodDays() . ' days')->format('Y-m-d'), null
            );
            $state['loans'][] = $loan->toArray();
        });
    }

    public function returnBook(array $input): void
    {
        $id = Validation::text($input, 'id', 'ID transaksi', 2, 40);
        $this->store->transaction(static function (array &$state) use ($id): void {
            foreach ($state['loans'] as &$raw) {
                if ($raw['id'] !== $id) { continue; }
                if ($raw['returned_at'] !== null) {
                    throw new DomainException('Buku tersebut sudah dikembalikan.');
                }
                $raw['returned_at'] = date('Y-m-d');
                return;
            }
            throw new DomainException('Transaksi tidak ditemukan.');
        });
    }
}
