<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create permissions
        $permissions = [
            'view employee',
            'create employee',
            'edit employee',
            'delete employee',
            'view role',
            'create role',
            'edit role',
            'delete role',
            'view permission',
            'create permission',
            'edit permission',
            'delete permission',
            'view bank',
            'create bank',
            'edit bank',
            'delete bank',
            'view firm',
            'create firm',
            'edit firm',
            'delete firm',
            'view rto agent',
            'create rto agent',
            'edit rto agent',
            'delete rto agent',
            'view insurance',
            'create insurance',
            'edit insurance',
            'delete insurance',
            'view vehicle manufacture',
            'create vehicle manufacture',
            'edit vehicle manufacture',
            'delete vehicle manufacture',
            'view branch',
            'create branch',
            'edit branch',
            'delete branch',
            'view city',
            'create city',
            'edit city',
            'delete city',
            'view lead',
            'create lead',
            'edit lead',
            'delete lead',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $teamLeadRole = Role::firstOrCreate(['name' => 'Team Lead']);
        $teamLeadRole->syncPermissions([
            'view employee', 'view rto agent', 'view insurance', 'view vehicle manufacture', 'view bank', 'view firm',
            'view lead', 'create lead', 'edit lead'
        ]);

        $teleCallerRole = Role::firstOrCreate(['name' => 'Tele Caller']);
        $teleCallerRole->syncPermissions([
            'view rto agent', 'view insurance',
            'view lead', 'edit lead'
        ]);


        // Create users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'employee_type' => 'Admin',
            ]
        );
        $admin->assignRole($adminRole);

        // Create Tele Caller users
        $teleCaller1 = User::firstOrCreate(
            ['email' => 'telecaller1@example.com'],
            [
                'name' => 'Tele Caller 1',
                'password' => Hash::make('password'),
                'employee_type' => 'Tele Caller',
            ]
        );
        $teleCaller1->assignRole($teleCallerRole);

        $teleCaller2 = User::firstOrCreate(
            ['email' => 'telecaller2@example.com'],
            [
                'name' => 'Tele Caller 2',
                'password' => Hash::make('password'),
                'employee_type' => 'Tele Caller',
            ]
        );
        $teleCaller2->assignRole($teleCallerRole);
    }
}
