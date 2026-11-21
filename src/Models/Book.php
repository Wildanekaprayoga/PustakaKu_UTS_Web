<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Borrowable;

/** Parent abstract: encapsulation lewat private fields, inheritance untuk jenis e-book. */
abstract class Book implements Borrowable
{
    private string $id;
    private string $title;
    private string $author;
    private string $category;
    private int $year;
    private string $description;
    private string $accent;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        int $year,
        string $description,
        string $accent
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->category = $category;
        $this->year = $year;
        $this->description = $description;
        $this->accent = $accent;
    }

    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getCategory(): string { return $this->category; }
    public function getYear(): int { return $this->year; }
    public function getDescription(): string { return $this->description; }
    public function getAccent(): string { return $this->accent; }

    abstract public function getKind(): string;
    abstract public function getKindLabel(): string;

    /** Serialisasi kembali ke penyimpanan JSON. */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'category' => $this->category,
            'year' => $this->year,
            'description' => $this->description,
            'accent' => $this->accent,
            'kind' => $this->getKind(),
        ];
    }
}
