<?php
declare(strict_types=1);

namespace App\Models;

/** Encapsulation data member. */
final class Member
{
    private string $id;
    private string $name;
    private string $email;

    public function __construct(string $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function toArray(): array { return ['id' => $this->id, 'name' => $this->name, 'email' => $this->email]; }
    public static function fromArray(array $data): self
    {
        return new self((string) $data['id'], (string) $data['name'], (string) $data['email']);
    }
}
