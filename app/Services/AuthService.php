<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'nip_nik' => $data['nip_nik'],
                'phone' => $data['phone'],
            ]);

            $user->assignRole('applicant');

            return $user;
        });
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['identifier'])->orWhere('name', $data['identifier'])->orWhere('nip_nik', $data['identifier'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return null;
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token_sipd')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nip_nik' => $user->nip_nik,
                'phone' => $user->phone,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(User $user): bool
    {
        return $user->currentAccessToken()->delete();
    }
}
