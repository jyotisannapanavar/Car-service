<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::firstOrCreate(
            ['email' => 'admin@carservice.com'],
            [
                'name' => 'Car Service Demo',
                'email' => 'admin@carservice.com',
                'phone' => '+1234567890',
                'address' => '123 Main Street, City, Country',
                'is_active' => true,
            ]
        );

        $branch = Branch::firstOrCreate(
            [
                'organization_id' => $organization->id,
                'name' => 'Main Branch',
            ],
            [
                'organization_id' => $organization->id,
                'name' => 'Main Branch',
                'code' => 'MAIN',
                'email' => 'main@carservice.com',
                'phone' => '+1234567891',
                'address' => '123 Main Street, City, Country',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@carservice.com'],
            [
                'organization_id' => $organization->id,
                'branch_id' => $branch->id,
                'name' => 'Admin User',
                'email' => 'admin@carservice.com',
                'phone' => '+1234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@carservice.com'],
            [
                'organization_id' => $organization->id,
                'branch_id' => $branch->id,
                'name' => 'Branch Manager',
                'email' => 'manager@carservice.com',
                'phone' => '+1234567892',
                'password' => Hash::make('password'),
                'role' => 'branch_manager',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@carservice.com'],
            [
                'organization_id' => $organization->id,
                'branch_id' => $branch->id,
                'name' => 'Staff Member',
                'email' => 'staff@carservice.com',
                'phone' => '+1234567893',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );
    }
}
