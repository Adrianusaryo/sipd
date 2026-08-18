<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // Make Permissions

        $permissions = [
            // Master Project
            'manage-projects',

            // Master Request Document
            'create-request',
            'view-request',
            'view-all-request',
            'review-request',
            'approve-request',
            'reject-request',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Make Roles and Assign Permission

        // Role : Applicant
        $roleApplicant = Role::findOrCreate('applicant', 'api');
        $roleApplicant->givePermissionTo([
            'create-request',
            'view-request',
        ]);

        // Role : verificator
        $roleVerificator = Role::findOrCreate('verificator', 'api');
        $roleVerificator->givePermissionTo([
            'view-all-request',
            'review-request',
            'approve-request',
            'reject-request',
        ]);

        // Role : Admin
        $roleAdmin = Role::findOrCreate('admin', 'api');
        $roleAdmin->givePermissionTo(Permission::all());
    }
}
