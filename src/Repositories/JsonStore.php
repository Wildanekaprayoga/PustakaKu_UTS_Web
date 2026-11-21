<?php
declare(strict_types=1);

namespace App\Repositories;

use RuntimeException;

/**
 * Penyimpanan JSON tanpa DBMS/framework.
 * Semua pembacaan/penulisan memakai flock dan penyimpanan memakai rename atomik.
 * Ini menjaga konsistensi data buku, anggota, dan pinjaman sekaligus.
 */
final class JsonStore
{
    private string $file;
    private string $lockFile;

    public function __construct(string $file)
    {
        $this->file = $file;
        $dir = dirname($file);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Folder storage tidak bisa dibuat.');
        }
        $this->lockFile = $file . '.lock';
        $this->withLock(LOCK_EX, function (): void {
            if (!is_file($this->file)) {
                $this->saveUnlocked(SeedData::make());
            }
        });
    }

    public function read(): array
    {
        return $this->withLock(LOCK_SH, fn(): array => $this->loadUnlocked());
    }

    /**
     * Closure menerima array state by-reference.
     * Jika ada exception, perubahan tidak ditulis.
     */
    public function transaction(callable $operation): mixed
    {
        return $this->withLock(LOCK_EX, function () use ($operation): mixed {
            $data = $this->loadUnlocked();
            $result = $operation($data);
            $this->saveUnlocked($data);
            return $result;
        });
    }

    private function loadUnlocked(): array
    {
        $json = @file_get_contents($this->file);
        if ($json === false) {
            throw new RuntimeException('Gagal membaca database JSON.');
        }
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || !isset($data['books'], $data['members'], $data['loans'])) {
            throw new RuntimeException('Struktur database JSON tidak valid.');
        }
        return $data;
    }

    private function saveUnlocked(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $temp = tempnam(dirname($this->file), 'db_');
        if ($temp === false) {
            throw new RuntimeException('Tidak dapat membuat file sementara.');
        }
        try {
            if (file_put_contents($temp, $json . "\n") === false || !rename($temp, $this->file)) {
                throw new RuntimeException('Gagal menyimpan database JSON. Periksa izin folder storage.');
            }
        } finally {
            if (is_file($temp)) {
                unlink($temp);
            }
        }
    }

    private function withLock(int $mode, callable $callback): mixed
    {
        $lock = @fopen($this->lockFile, 'c');
        if ($lock === false) {
            throw new RuntimeException('File lock tidak dapat dibuat. Pastikan folder storage writable.');
        }
        try {
            if (!flock($lock, $mode)) {
                throw new RuntimeException('Gagal mengunci penyimpanan.');
            }
            try {
                return $callback();
            } finally {
                flock($lock, LOCK_UN);
            }
        } finally {
            fclose($lock);
        }
    }
}
