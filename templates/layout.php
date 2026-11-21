<?php
$headings = [
    'dashboard' => ['Dashboard', 'Ringkasan aktivitas perpustakaan digital'],
    'catalog' => ['Katalog E-Book', 'Kelola koleksi buku dan mulai peminjaman'],
    'members' => ['Data Anggota', 'Kelola identitas anggota perpustakaan'],
    'transactions' => ['Transaksi', 'Pantau peminjaman dan proses pengembalian'],
];
$menus = [
    'dashboard' => ['⌂', 'Dashboard'],
    'catalog' => ['▤', 'Katalog E-Book'],
    'members' => ['♧', 'Data Anggota'],
    'transactions' => ['⇄', 'Transaksi'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="description" content="PustakaKu — Prototipe sistem peminjaman e-book berbasis PHP OOP tanpa framework.">
    <title><?= e($headings[$page][0]) ?> — PustakaKu</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/app.js"></script>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" aria-label="Navigasi utama">
        <a class="brand" href="?page=dashboard" aria-label="Beranda PustakaKu">
            <span class="brand-mark" aria-hidden="true">▥</span>
            <span class="brand-text">Pustaka<span>Ku.</span><small>DIGITAL LIBRARY</small></span>
        </a>
        <div class="side-caption">MENU UTAMA</div>
        <nav class="nav-stack" aria-label="Halaman aplikasi">
            <?php foreach ($menus as $route => [$symbol, $label]): ?>
                <a href="?page=<?= e($route) ?>" class="nav-link <?= $page === $route ? 'is-active' : '' ?>" <?= $page === $route ? 'aria-current="page"' : '' ?>>
                    <span class="nav-symbol" aria-hidden="true"><?= e($symbol) ?></span>
                    <span><?= e($label) ?></span>
                    <?php if ($route === 'transactions' && $summary['active'] > 0): ?><span class="nav-count"><?= $summary['active'] ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-bottom">
            <div class="side-help"><span class="side-help-icon">✦</span><strong>Proyek UTS</strong><small>PHP 8 · OOP · Tanpa framework</small></div>
            <div class="profile"><span class="avatar">AD</span><span><strong>Admin PustakaKu</strong><small>Mode demonstrasi</small></span><span class="profile-dot" aria-hidden="true"></span></div>
        </div>
    </aside>
    <div class="main-area">
        <header class="topbar">
            <div class="breadcrumb">PERPUSTAKAAN <span>/</span> <?= e(strtoupper($headings[$page][0])) ?></div>
            <div class="top-actions"><span class="today-pill"><span class="online-dot"></span> Sistem aktif</span><span class="header-divider"></span><span class="header-date"><?= e(tanggal(date('Y-m-d'))) ?></span><span class="header-avatar">A</span></div>
        </header>
        <main id="main-content" class="page-content">
            <div class="page-heading"><div><p class="eyebrow">WELCOME TO PUSTAKAKU</p><h1><?= e($headings[$page][0]) ?></h1><p><?= e($headings[$page][1]) ?></p></div><div class="heading-tag">UTS PEMROGRAMAN WEB <span>2026/2027</span></div></div>
            <?php if ($flash): ?>
                <div class="alert <?= $flash['type'] === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><span aria-hidden="true"><?= $flash['type'] === 'error' ? '!' : '✓' ?></span><div><?= e($flash['message']) ?></div><button type="button" class="alert-close" aria-label="Tutup pesan" data-dismiss-alert>×</button></div>
            <?php endif; ?>
            <?php require __DIR__ . '/' . $page . '.php'; ?>
            <footer class="footer"><span>© <?= date('Y') ?> PustakaKu · Proyek UTS Pemrograman Web</span><span>HTML · CSS · JavaScript · PHP OOP · JSON</span></footer>
        </main>
    </div>
</div>
</body>
</html>
