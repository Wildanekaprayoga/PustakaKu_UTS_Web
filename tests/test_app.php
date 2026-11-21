<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/autoload.php';
date_default_timezone_set('Asia/Jakarta');

use App\Models\Book;
use App\Models\BookFactory;
use App\Models\EBook;
use App\Models\PremiumEBook;
use App\Repositories\JsonStore;
use App\Services\CatalogService;
use App\Services\MemberService;
use App\Services\LoanService;

$dir = sys_get_temp_dir() . '/pustakaku_test_' . bin2hex(random_bytes(5));
mkdir($dir, 0775, true);
$store = new JsonStore($dir . '/database.json');
$catalog = new CatalogService($store);
$members = new MemberService($store);
$loans = new LoanService($store);
$checks = 0;

function check(bool $assertion, string $message): void
{
    global $checks;
    if (!$assertion) {
        throw new RuntimeException('FAIL: ' . $message);
    }
    $checks++;
    echo "[OK] {$message}\n";
}
function expectError(callable $fn, string $message): void
{
    $caught = false;
    try { $fn(); } catch (DomainException | InvalidArgumentException $error) { $caught = true; }
    check($caught, $message);
}
function itemById(array $items, string $id): ?array
{
    foreach ($items as $item) { if ($item['id'] === $id) return $item; }
    return null;
}

try {
    $initial = $store->read();
    check(count($initial['books']) === 8, 'Seed menciptakan 8 buku');
    check(count($initial['members']) === 3, 'Seed menciptakan 3 anggota');
    check(count($initial['loans']) === 2, 'Seed menciptakan 2 transaksi');

    $regular = BookFactory::fromArray($initial['books'][0]);
    $premium = BookFactory::fromArray($initial['books'][1]);
    check($regular instanceof Book && $regular instanceof EBook && $regular->getLoanPeriodDays() === 7, 'Inheritance dan polymorphism reguler 7 hari');
    check($premium instanceof Book && $premium instanceof PremiumEBook && $premium->getLoanPeriodDays() === 14, 'Inheritance dan polymorphism premium 14 hari');
    $catalog->save(['title' => 'Pemrograman Web UTS', 'author' => 'Mahasiswa UMM', 'category' => 'Teknologi', 'year' => '2026', 'description' => 'Praktikum fullstack.', 'accent' => 'blue', 'kind' => 'standard']);
    $updated = $store->read();
    check(count($updated['books']) === 9, 'Tambah katalog disimpan ke JSON');
    $book = end($updated['books']);
    $bookId = $book['id'];
    $catalog->save(['id' => $bookId, 'title' => 'Pemrograman Web UTS Diperbarui', 'author' => 'Mahasiswa UMM', 'category' => 'Teknologi', 'year' => '2026', 'description' => 'Revisi.', 'accent' => 'teal', 'kind' => 'premium']);
    $edited = itemById($store->read()['books'], $bookId);
    check($edited['title'] === 'Pemrograman Web UTS Diperbarui' && $edited['kind'] === 'premium', 'Edit buku berhasil');

    $members->save(['name' => 'Tester Pemrograman', 'email' => 'tester@example.com']);
    $state = $store->read();
    check(count($state['members']) === 4, 'Tambah anggota berhasil');
    $member = end($state['members']);
    $memberId = $member['id'];
    expectError(static fn() => $members->save(['name' => 'Duplikat Tester', 'email' => 'TESTER@example.com']), 'Email ganda ditolak tanpa memperdulikan kapital');
    expectError(static fn() => $members->save(['name' => 'Uji Email', 'email' => 'invalid-email']), 'Format email tidak valid ditolak');
    $members->save(['id' => $memberId, 'name' => 'Tester Diubah', 'email' => 'tester2@example.com']);
    check(itemById($store->read()['members'], $memberId)['name'] === 'Tester Diubah', 'Edit anggota berhasil');

    $loans->borrow(['book_id' => $bookId, 'member_id' => $memberId]);
    $state = $store->read();
    $loan = end($state['loans']);
    check(count($state['loans']) === 3 && $loan['returned_at'] === null, 'Peminjaman tersimpan');
    $expectedDue = (new DateTimeImmutable('today'))->modify('+14 days')->format('Y-m-d');
    check($loan['due_at'] === $expectedDue, 'Tenggat 14 hari dari subclass premium');
    expectError(static fn() => $loans->borrow(['book_id' => $bookId, 'member_id' => 'MB003']), 'Peminjaman ganda pada e-book yang sama ditolak');
    expectError(static fn() => $catalog->delete(['id' => $bookId]), 'Buku dengan peminjaman aktif tidak dapat dihapus');
    expectError(static fn() => $members->delete(['id' => $memberId]), 'Anggota dengan peminjaman aktif tidak dapat dihapus');
    $loans->returnBook(['id' => $loan['id']]);
    check(itemById($store->read()['loans'], $loan['id'])['returned_at'] !== null, 'Pengembalian dicatat');
    expectError(static fn() => $loans->returnBook(['id' => $loan['id']]), 'Double-return ditolak');
    $catalog->delete(['id' => $bookId]);
    check(count($store->read()['books']) === 8, 'Hapus buku sukses setelah dikembalikan');
    $members->delete(['id' => $memberId]);
    check(count($store->read()['members']) === 3, 'Hapus anggota sukses setelah pinjaman selesai');
    $reopened = new JsonStore($dir . '/database.json');
    check(count($reopened->read()['loans']) === 3, 'Data JSON bertahan setelah Store dibuat ulang');
    check(itemById($reopened->read()['loans'], $loan['id'])['book_title'] === 'Pemrograman Web UTS Diperbarui', 'Snapshot judul transaksi tetap tersimpan setelah buku dihapus');

    // Uji batas anggota 3 buku aktif dan durasi reguler, tanpa memengaruhi hasil di atas.
    $limitStore = new JsonStore($dir . '/limit.json');
    $limitLoans = new LoanService($limitStore);
    foreach (['BK001', 'BK003', 'BK004'] as $id) {
        $limitLoans->borrow(['book_id' => $id, 'member_id' => 'MB003']);
    }
    $limitState = $limitStore->read();
    $regularLoan = end($limitState['loans']);
    check(count($limitState['loans']) === 5, 'Member dapat meminjam tiga e-book tambahan');
    expectError(static fn() => $limitLoans->borrow(['book_id' => 'BK005', 'member_id' => 'MB003']), 'Batas 3 pinjaman aktif ditegakkan');
    $standardLoan = itemById($limitState['loans'], 'LN001');
    // LN001 data awal; cari pinjaman baru BK001 untuk uji masa 7 hari.
    $newRegular = null;
    foreach ($limitState['loans'] as $item) {
        if ($item['book_id'] === 'BK001' && $item['member_id'] === 'MB003') $newRegular = $item;
    }
    check($newRegular !== null && $newRegular['due_at'] === (new DateTimeImmutable('today'))->modify('+7 days')->format('Y-m-d'), 'Tenggat reguler tercatat 7 hari');

    echo "\nSELESAI: {$checks} pengujian berhasil.\n";
} finally {
    foreach (glob($dir . '/*') as $path) { unlink($path); }
    rmdir($dir);
}
