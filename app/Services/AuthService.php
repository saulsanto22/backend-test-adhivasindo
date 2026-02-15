<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
   
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->generateTokenResponse($user, 'Registrasi berhasil.');
    }

    public function attempt(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $this->generateTokenResponse($user, 'Login berhasil.');
    }

 
    private function generateTokenResponse(User $user, string $message): array
    {
        $token = $user->createToken('api')->plainTextToken;

        return [
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $user,
        ];
    }
}

