<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Ambil semua user dengan paginasi dinamis.
     *
     * @param  int  $perPage  Jumlah data per halaman (default: 10)
     * @param  string|null  $search  Kata kunci pencarian (nama atau email)
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
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
