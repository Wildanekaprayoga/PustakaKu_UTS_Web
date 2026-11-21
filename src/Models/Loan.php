<?php
declare(strict_types=1);

namespace App\Models;

/** Model transaksi, menyimpan snapshot judul/nama agar histori tetap dapat ditampilkan. */
final class Loan
{
    private string $id;
    private string $bookId;
    private string $memberId;
    private string $bookTitle;
    private string $memberName;
    private string $borrowedAt;
    private string $dueAt;
    private ?string $returnedAt;

    public function __construct(
        string $id, string $bookId, string $memberId, string $bookTitle,
        string $memberName, string $borrowedAt, string $dueAt, ?string $returnedAt
    ) {
        $this->id = $id;
        $this->bookId = $bookId;
        $this->memberId = $memberId;
        $this->bookTitle = $bookTitle;
        $this->memberName = $memberName;
        $this->borrowedAt = $borrowedAt;
        $this->dueAt = $dueAt;
        $this->returnedAt = $returnedAt;
    }

    public function getId(): string { return $this->id; }
    public function getBookId(): string { return $this->bookId; }
    public function getMemberId(): string { return $this->memberId; }
    public function getBookTitle(): string { return $this->bookTitle; }
    public function getMemberName(): string { return $this->memberName; }
    public function getBorrowedAt(): string { return $this->borrowedAt; }
    public function getDueAt(): string { return $this->dueAt; }
    public function getReturnedAt(): ?string { return $this->returnedAt; }
    public function isActive(): bool { return $this->returnedAt === null; }
    public function getStatus(): string
    {
        if ($this->returnedAt !== null) {
            return 'Dikembalikan';
        }
        return date('Y-m-d') > $this->dueAt ? 'Terlambat' : 'Dipinjam';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id, 'book_id' => $this->bookId,
            'member_id' => $this->memberId, 'book_title' => $this->bookTitle,
            'member_name' => $this->memberName, 'borrowed_at' => $this->borrowedAt,
            'due_at' => $this->dueAt, 'returned_at' => $this->returnedAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['id'], (string) $data['book_id'], (string) $data['member_id'],
            (string) $data['book_title'], (string) $data['member_name'],
            (string) $data['borrowed_at'], (string) $data['due_at'],
            isset($data['returned_at']) ? (string) $data['returned_at'] : null
        );
    }
}
