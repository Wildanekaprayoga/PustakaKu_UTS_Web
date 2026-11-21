<section class="hero-banner" aria-labelledby="hero-title">
    <div class="hero-copy"><div class="hero-pill">✦ SMART DIGITAL LIBRARY</div><h2 id="hero-title">Satu tempat untuk<br><em>semua cerita.</em></h2><p>Kelola koleksi e-book, anggota, dan transaksi peminjaman dengan cara yang lebih mudah.</p><a class="btn btn-white" href="?page=catalog">Jelajahi Katalog <span aria-hidden="true">↗</span></a></div>
    <div class="hero-visual" aria-hidden="true"><div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div><div class="hero-book book-back"><span>KNOWLEDGE</span></div><div class="hero-book book-front"><span>THE<br>FUTURE<br>IS<br>YOURS.</span><small>READ · LEARN · GROW</small></div><div class="hero-star star-one">✦</div><div class="hero-star star-two">✧</div></div>
</section>
<section class="stats-grid" aria-label="Statistik perpustakaan">
    <article class="stat-card"><div class="stat-top"><span>Total E-Book</span><span class="stat-icon icon-blue">▤</span></div><strong><?= $summary['books'] ?></strong><div class="stat-note"><span class="dot dot-blue"></span> Koleksi terdaftar</div></article>
    <article class="stat-card"><div class="stat-top"><span>Anggota Aktif</span><span class="stat-icon icon-purple">♧</span></div><strong><?= $summary['members'] ?></strong><div class="stat-note"><span class="dot dot-purple"></span> Pengguna terdaftar</div></article>
    <article class="stat-card"><div class="stat-top"><span>Sedang Dipinjam</span><span class="stat-icon icon-orange">⇄</span></div><strong><?= $summary['active'] ?></strong><div class="stat-note"><span class="dot dot-orange"></span> Transaksi berjalan</div></article>
    <article class="stat-card"><div class="stat-top"><span>Tersedia</span><span class="stat-icon icon-green">✓</span></div><strong><?= $summary['available'] ?></strong><div class="stat-note"><span class="dot dot-green"></span> Siap dipinjam</div></article>
</section>
<div class="dashboard-grid">
    <section class="panel" aria-labelledby="recent-title">
        <div class="panel-header"><div><div class="section-kicker">AKTIVITAS</div><h2 id="recent-title">Transaksi Terbaru</h2></div><a class="text-link" href="?page=transactions">Lihat Semua <span aria-hidden="true">↗</span></a></div>
        <?php if (count($loans) === 0): ?><div class="empty-state"><div>⇄</div><strong>Belum ada transaksi</strong><p>Pinjam e-book untuk melihat aktivitas.</p></div>
        <?php else: ?><div class="recent-list">
            <?php foreach (array_slice($loans, 0, 4) as $loan): ?>
                <div class="recent-row"><div class="recent-icon">▤</div><div class="recent-info"><strong><?= e($loan->getBookTitle()) ?></strong><small><?= e($loan->getMemberName()) ?> · <?= e(tanggal($loan->getBorrowedAt())) ?></small></div><span class="status-badge status-<?= $loan->getStatus() === 'Dikembalikan' ? 'returned' : ($loan->getStatus() === 'Terlambat' ? 'overdue' : 'active') ?>"><?= e($loan->getStatus()) ?></span></div>
            <?php endforeach; ?>
        </div><?php endif; ?>
    </section>
    <section class="panel quick-panel" aria-labelledby="quick-title">
        <div class="panel-header"><div><div class="section-kicker">AKSES CEPAT</div><h2 id="quick-title">Mulai dari sini</h2></div></div>
        <p class="quick-description">Semua yang kamu butuhkan untuk mengelola perpustakaan.</p>
        <a href="?page=catalog" class="quick-link"><span class="quick-icon quick-blue">▤</span><span><strong>Kelola E-Book</strong><small>Tambah, edit, cari, dan pinjam buku</small></span><span class="quick-arrow">↗</span></a>
        <a href="?page=members" class="quick-link"><span class="quick-icon quick-purple">♧</span><span><strong>Kelola Anggota</strong><small>Tambah dan perbarui data anggota</small></span><span class="quick-arrow">↗</span></a>
        <a href="?page=transactions" class="quick-link"><span class="quick-icon quick-orange">⇄</span><span><strong>Cek Transaksi</strong><small>Pantau dan kembalikan e-book</small></span><span class="quick-arrow">↗</span></a>
    </section>
</div>
<section class="bottom-tip"><span class="bottom-tip-icon">✦</span><div><strong>Tahukah kamu?</strong><p>E-book reguler memiliki masa pinjam <b>7 hari</b>, sedangkan premium <b>14 hari</b>. Ini diatur melalui <i>polymorphism</i> pada PHP OOP.</p></div></section>
