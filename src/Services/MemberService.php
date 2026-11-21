<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use App\Repositories\JsonStore;
use App\Support\Validation;
use DomainException;

final class MemberService
{
    public function __construct(private JsonStore $store) {}

    public function save(array $input): void
    {
        $id = Validation::optionalText($input, 'id', 'ID anggota', 40);
        $name = Validation::text($input, 'name', 'Nama anggota', 3, 100);
        $email = Validation::email($input);
        $this->store->transaction(static function (array &$state) use ($id, $name, $email): void {
            $index = null;
            foreach ($state['members'] as $i => $member) {
                if ($member['id'] === $id) { $index = $i; }
                if ($member['email'] === $email && $member['id'] !== $id) {
                    throw new DomainException('Email sudah terdaftar oleh anggota lain.');
                }
            }
            if ($id !== '' && $index === null) {
                throw new DomainException('Anggota yang ingin diedit tidak ditemukan.');
            }
            $member = new Member($id !== '' ? $id : 'MB' . strtoupper(bin2hex(random_bytes(5))), $name, $email);
            if ($index === null) {
                $state['members'][] = $member->toArray();
            } else {
                $state['members'][$index] = $member->toArray();
            }
        });
    }

    public function delete(array $input): void
    {
        $id = Validation::text($input, 'id', 'ID anggota', 2, 40);
        $this->store->transaction(static function (array &$state) use ($id): void {
            foreach ($state['loans'] as $loan) {
                if ($loan['member_id'] === $id && $loan['returned_at'] === null) {
                    throw new DomainException('Anggota masih memiliki pinjaman. Selesaikan dahulu.');
                }
            }
            $initial = count($state['members']);
            $state['members'] = array_values(array_filter($state['members'], static fn(array $member): bool => $member['id'] !== $id));
            if (count($state['members']) === $initial) {
                throw new DomainException('Anggota tidak ditemukan.');
            }
        });
    }
}
