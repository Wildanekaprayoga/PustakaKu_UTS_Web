<section class="catalog-toolbar">
    <div class="section-title-block"><div class="section-kicker">DIGITAL COLLECTION</div><h2>Jelajahi Koleksi <span class="count-chip"><?= count($books) ?> Buku</span></h2><p>Temukan bacaan menarik yang bisa kamu pinjam.</p></div>
    <button class="btn btn-primary" type="button" data-open="bookDialog">+ Tambah E-Book</button>
</section>
<div class="toolbar-controls">
    <label class="search-box"><span aria-hidden="true">⌕</span><span class="sr-only">Cari e-book</span><input id="catalogSearch" type="search" autocomplete="off" placeholder="Cari judul atau penulis..." aria-label="Cari judul atau penulis"></label>
    <div class="filter-chips" role="group" aria-label="Filter kategori">
        <button type="button" class="filter-chip active" data-category="all" aria-pressed="true">Semua</button>
        <?php foreach ($categories as $category): ?><button type="button" class="filter-chip" data-category="<?= e($category) ?>" aria-pressed="false"><?= e($category) ?></button><?php endforeach; ?>
    </div>
</div>
<div class="catalog-results"><span id="catalogResultCount" role="status"><?= count($books) ?> e-book ditampilkan</span><span>Pinjam dengan sekali klik</span></div>
<section class="books-grid" id="booksGrid" aria-label="Daftar e-book">
    <?php foreach ($books as $book): ?>
        <?php $available = !isset($activeBookIds[$book->getId()]); ?>
        <article class="book-card" data-book-card data-category-value="<?= e($book->getCategory()) ?>" data-search-value="<?= e($book->getTitle() . ' ' . $book->getAuthor()) ?>">
            <div class="book-art cover-<?= e($book->getAccent()) ?>"><div class="cover-art-lines" aria-hidden="true"></div><span class="cover-top">PUSTAKAKU EDITION</span><strong><?= e($book->getTitle()) ?></strong><small><?= e($book->getAuthor()) ?></small><span class="cover-mark" aria-hidden="true">✦</span></div>
            <div class="book-body"><div class="book-meta"><span class="book-category"><?= e($book->getCategory()) ?></span><span class="tier <?= $book->getKind() === 'premium' ? 'tier-premium' : '' ?>"><?= $book->getKind() === 'premium' ? '✧ ' : '' ?><?= e($book->getKindLabel()) ?></span></div>
                <h3><?= e($book->getTitle()) ?></h3><p class="book-author">oleh <?= e($book->getAuthor()) ?> · <?= $book->getYear() ?></p><p class="book-description"><?= e($book->getDescription()) ?></p>
                <div class="book-availability"><span class="availability-dot <?= $available ? 'is-available' : 'is-busy' ?>"></span><?= $available ? 'Tersedia' : 'Sedang dipinjam' ?><span class="duration"><?= $book->getLoanPeriodDays() ?> hari</span></div>
                <div class="book-actions"><button class="btn btn-primary btn-borrow" type="button" data-borrow-book data-id="<?= e($book->getId()) ?>" data-title="<?= e($book->getTitle()) ?>" data-days="<?= $book->getLoanPeriodDays() ?>" <?= !$available || count($members) === 0 ? 'disabled' : '' ?>><?= $available ? 'Pinjam Buku →' : 'Tidak Tersedia' ?></button><button class="icon-button" type="button" aria-label="Edit <?= e($book->getTitle()) ?>" title="Edit" data-edit-book
                        data-id="<?= e($book->getId()) ?>" data-title="<?= e($book->getTitle()) ?>" data-author="<?= e($book->getAuthor()) ?>" data-category="<?= e($book->getCategory()) ?>" data-year="<?= $book->getYear() ?>" data-description="<?= e($book->getDescription()) ?>" data-accent="<?= e($book->getAccent()) ?>" data-kind="<?= e($book->getKind()) ?>">✎</button>
                    <form method="post" data-confirm="Hapus buku ini? Riwayat transaksi lama tetap tersimpan."><input type="hidden" name="csrf_token" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="book.delete"><input type="hidden" name="return_page" value="catalog"><input type="hidden" name="id" value="<?= e($book->getId()) ?>"><button type="submit" class="icon-button icon-danger" aria-label="Hapus <?= e($book->getTitle()) ?>" title="Hapus">×</button></form>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<div class="empty-state empty-filter" id="catalogEmpty" hidden><div>⌕</div><strong>Tidak ada buku yang cocok</strong><p>Coba kata kunci atau kategori lain.</p></div>
<?php if (count($members) === 0): ?><p class="inline-note">Belum ada anggota. <a href="?page=members">Tambahkan anggota</a> sebelum membuat peminjaman.</p><?php endif; ?>
<dialog id="bookDialog" class="modal" aria-labelledby="bookDialogTitle">
    <div class="modal-top"><div><div class="section-kicker">KOLEKSI DIGITAL</div><h2 id="bookDialogTitle">Tambah E-Book</h2><p>Lengkapi data buku yang akan ditampilkan.</p></div><button type="button" data-close-dialog class="modal-x" aria-label="Tutup">×</button></div>
    <form method="post" id="bookForm" class="modal-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="book.save"><input type="hidden" name="return_page" value="catalog"><input type="hidden" name="id" value="">
        <div class="form-field"><label for="bookTitle">Judul E-Book <span>*</span></label><input id="bookTitle" name="title" required minlength="2" maxlength="100" placeholder="Mis. Pemrograman PHP Modern"></div>
        <div class="form-field"><label for="bookAuthor">Nama Penulis <span>*</span></label><input id="bookAuthor" name="author" required minlength="2" maxlength="100" placeholder="Nama penulis"></div>
        <div class="form-two">
            <div class="form-field"><label for="bookCategory">Kategori <span>*</span></label><select id="bookCategory" name="category" required><?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?= e($category) ?></option><?php endforeach; ?></select></div>
            <div class="form-field"><label for="bookYear">Tahun Terbit <span>*</span></label><input id="bookYear" name="year" type="number" min="1900" max="<?= date('Y') + 1 ?>" value="<?= date('Y') ?>" required></div>
        </div>
        <div class="form-two">
            <div class="form-field"><label for="bookKind">Jenis E-Book <span>*</span></label><select id="bookKind" name="kind"><option value="standard">Reguler — 7 hari</option><option value="premium">Premium — 14 hari</option></select></div>
            <div class="form-field"><label for="bookAccent">Warna Sampul</label><select id="bookAccent" name="accent"><option value="blue">Biru</option><option value="mint">Hijau Mint</option><option value="orange">Jingga</option><option value="sand">Krem</option><option value="purple">Ungu</option><option value="rose">Merah Muda</option><option value="indigo">Indigo</option><option value="teal">Teal</option></select></div>
        </div>
        <div class="form-field"><label for="bookDescription">Deskripsi Singkat</label><textarea id="bookDescription" name="description" maxlength="300" rows="3" placeholder="Ringkasan isi e-book..."></textarea></div>
        <div class="modal-footer"><button type="button" class="btn btn-muted" data-close-dialog>Batal</button><button type="submit" class="btn btn-primary">Simpan E-Book</button></div>
    </form>
</dialog>
<dialog id="borrowDialog" class="modal modal-small" aria-labelledby="borrowDialogTitle">
    <div class="modal-top"><div><div class="section-kicker">TRANSAKSI BARU</div><h2 id="borrowDialogTitle">Pinjam E-Book</h2><p id="borrowBookName">Pilih anggota yang meminjam.</p></div><button type="button" data-close-dialog class="modal-x" aria-label="Tutup">×</button></div>
    <form method="post" class="modal-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="loan.borrow"><input type="hidden" name="return_page" value="transactions"><input type="hidden" name="book_id" id="borrowBookId">
        <div class="loan-summary"><span>Durasi peminjaman</span><strong id="borrowDuration">7 hari</strong><small>Tenggat dihitung otomatis oleh PHP OOP.</small></div>
        <div class="form-field"><label for="borrowMember">Pilih Anggota <span>*</span></label><select name="member_id" id="borrowMember" required><option value="">— Pilih anggota —</option><?php foreach ($members as $member): ?><option value="<?= e($member->getId()) ?>"><?= e($member->getName()) ?> (<?= e($member->getEmail()) ?>)</option><?php endforeach; ?></select></div>
        <div class="modal-footer"><button type="button" class="btn btn-muted" data-close-dialog>Batal</button><button type="submit" class="btn btn-primary">Konfirmasi Pinjam →</button></div>
    </form>
</dialog>
