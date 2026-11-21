<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';
date_default_timezone_set('Asia/Jakarta');

try {
    $app = new \App\Http\Application(dirname(__DIR__) . '/storage/database.json');
    $app->run();
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Kesalahan Server</title><body style="font:16px system-ui;padding:2rem"><h1>Aplikasi belum dapat dijalankan</h1><p>Periksa PHP 8.x, ekstensi mbstring, serta izin tulis folder <code>storage/</code>.</p></body></html>';
}
