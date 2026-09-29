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
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $roleUser = Role::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);

        // 2. Akun Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sipd.go.id'],
            [
                'username' => 'Super Admin SIPD',
                'password' => Hash::make('password123'),
                'nip_nik' => '199001012020121001',
                'phone' => '081234567890',
            ]
        );
        $superAdmin->syncRoles([$roleSuperAdmin]);

        // 3. Akun Penilai / Verifikator
        $admin = User::firstOrCreate(
            ['email' => 'admin@sipd.go.id'],
            [
                'username' => 'Admin SIPD',
                'password' => Hash::make('password123'),
                'nip_nik' => '198505152015031002',
                'phone' => '081298765432',
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        // 4. Akun Pemohon
        $user = User::firstOrCreate(
            ['email' => 'rajajawa@sipd.go.id'],
            [
                'username' => 'Raja Jawa',
                'password' => Hash::make('password123'),
                'nip_nik' => '3173012345670001',
                'phone' => '085712345678',
            ]
        );
        $user->syncRoles([$roleUser]);
    }
}
