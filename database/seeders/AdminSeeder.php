<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'admin_id' => 'ADMIN001',
                'email' => 'admin@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'status' => 'active',
            ],
            [
                'admin_id' => 'ADMIN002',
                'email' => 'admin2@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'status' => 'active',
            ],
        ];

        foreach ($admins as $admin) {
            Admin::firstOrCreate(
                ['admin_id' => $admin['admin_id']],
                $admin
            );
        }
    }
}
