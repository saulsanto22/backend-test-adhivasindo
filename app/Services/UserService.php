<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Ambil semua user.
     */
    public function getAll(): Collection
    {
        return User::all();
    }

    /**
     * Cari user berdasarkan ID.
     */
    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Buat user baru.
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * Update data user.
     */
    public function update(User $user, array $data): User
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->fill($data);
        $user->save();

        return $user->fresh();
    }

    /**
     * Hapus user.
     */
    public function delete(User $user): void
    {
        $user->delete();
    }
}
