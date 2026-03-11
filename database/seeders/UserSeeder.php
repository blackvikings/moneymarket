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
        // Define permissions with their modules
        $permissions = [
            // Employee Module
            'show employee option' => 'Employees',
            'view employee' => 'Employees',
            'create employee' => 'Employees',
            'edit employee' => 'Employees',
            'delete employee' => 'Employees',
            'export employee' => 'Employees',
            'view role' => 'Employees',
            'create role' => 'Employees',
            'edit role' => 'Employees',
            'delete role' => 'Employees',
            'view permission' => 'Employees',
            'create permission' => 'Employees',
            'edit permission' => 'Employees',
            'delete permission' => 'Employees',

            // Masters Module
            'show master option' => 'Masters',
            'view bank' => 'Masters',
            'create bank' => 'Masters',
            'edit bank' => 'Masters',
            'delete bank' => 'Masters',
            'export bank' => 'Masters',
            'view firm' => 'Masters',
            'create firm' => 'Masters',
            'edit firm' => 'Masters',
            'delete firm' => 'Masters',
            'export firm' => 'Masters',
            'view rto agent' => 'Masters',
            'create rto agent' => 'Masters',
            'edit rto agent' => 'Masters',
            'delete rto agent' => 'Masters',
            'export rto agent' => 'Masters',
            'view insurance' => 'Masters',
            'create insurance' => 'Masters',
            'edit insurance' => 'Masters',
            'delete insurance' => 'Masters',
            'export insurance' => 'Masters',
            'view vehicle manufacture' => 'Masters',
            'create vehicle manufacture' => 'Masters',
            'edit vehicle manufacture' => 'Masters',
            'delete vehicle manufacture' => 'Masters',
            'export vehicle manufacture' => 'Masters',
            'view branch' => 'Masters',
            'create branch' => 'Masters',
            'edit branch' => 'Masters',
            'delete branch' => 'Masters',
            'view city' => 'Masters',
            'create city' => 'Masters',
            'edit city' => 'Masters',
            'delete city' => 'Masters',
            'export city' => 'Masters',

            // LMS Module (Leads)
            'show lms option' => 'LMS',
            'view lead' => 'LMS',
            'create lead' => 'LMS',
            'edit lead' => 'LMS',
            'delete lead' => 'LMS',
            'export lead' => 'LMS',
            'import lead' => 'LMS',
            'bulk assign lead' => 'LMS',
            'view user' => 'LMS', // Used in lead assignment dropdowns
            'create user' => 'LMS',
            'edit user' => 'LMS',
            'delete user' => 'LMS',

            // CRM Module
            'show crm option' => 'CRM',
            'view crm' => 'CRM',
            'edit crm' => 'CRM',
            'export crm' => 'CRM',
            'view rto' => 'CRM',
            'create rto' => 'CRM',
            'edit rto' => 'CRM',
            'export rto' => 'CRM',
            'view insurance lead' => 'CRM',
            'create insurance lead' => 'CRM',
            'edit insurance lead' => 'CRM',
            'export insurance lead' => 'CRM',
            'view referral log' => 'CRM',
        ];

        foreach ($permissions as $permissionName => $module) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['module' => $module]
            );
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(array_keys($permissions));

        $teamLeadRole = Role::firstOrCreate(['name' => 'Team Lead']);
        $teamLeadRole->syncPermissions([
            'show employee option', 'view employee',
            'show master option', 'view rto agent', 'view insurance', 'view vehicle manufacture', 'view bank', 'view firm',
            'show lms option', 'view lead', 'create lead', 'edit lead', 'bulk assign lead', 'export lead', 'import lead',
            'show crm option', 'view crm', 'edit crm', 'export crm',
            'view rto', 'create rto', 'edit rto', 'export rto',
            'view insurance lead', 'create insurance lead', 'edit insurance lead', 'export insurance lead',
            'view referral log',
        ]);

        $teleCallerRole = Role::firstOrCreate(['name' => 'Tele Caller']);
        $teleCallerRole->syncPermissions([
            'show lms option', 'view lead', 'edit lead',
        ]);


        // Create users
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'employee_type' => 'Admin',
                'is_first_login' => false,
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
                'is_first_login' => true,
            ]
        );
        $teleCaller1->assignRole($teleCallerRole);

        $teleCaller2 = User::firstOrCreate(
            ['email' => 'telecaller2@example.com'],
            [
                'name' => 'Tele Caller 2',
                'password' => Hash::make('password'),
                'employee_type' => 'Tele Caller',
                'is_first_login' => true,
            ]
        );
        $teleCaller2->assignRole($teleCallerRole);
    }
}
