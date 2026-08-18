<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'api';

        // 1. Ambil atau buat role jika belum ada
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $roleVerificator = Role::firstOrCreate(['name' => 'verificator', 'guard_name' => $guard]);
        $roleApplicant = Role::firstOrCreate(['name' => 'applicant', 'guard_name' => $guard]);

        // 2. Akun Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@sipd.go.id'],
            [
                'name' => 'Administrator SIPD',
                'password' => Hash::make('password123'),
                'nip_nik' => '199001012020121001',
                'phone' => '081234567890',
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        // 3. Akun Penilai / Verifikator
        $verificator = User::firstOrCreate(
            ['email' => 'verificator@sipd.go.id'],
            [
                'name' => 'Verificator',
                'password' => Hash::make('password123'),
                'nip_nik' => '198505152015031002',
                'phone' => '081298765432',
            ]
        );
        $verificator->syncRoles([$roleVerificator]);

        // 4. Akun Pemohon
        $applicant = User::firstOrCreate(
            ['email' => 'applicant@sipd.go.id'],
            [
                'name' => 'Raja Jawa',
                'password' => Hash::make('password123'),
                'nip_nik' => '3173012345670001',
                'phone' => '085712345678',
            ]
        );
        $applicant->syncRoles([$roleApplicant]);
    }
}
