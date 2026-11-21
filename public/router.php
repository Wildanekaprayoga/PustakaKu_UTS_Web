<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$target = __DIR__ . ($path === '/' ? '/index.php' : $path);
if ($path !== '/' && is_file($target)) {
    return false;
}
require __DIR__ . '/index.php';
