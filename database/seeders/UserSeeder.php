<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'user@company.com',
            'password' => Hash::make('password'),
            'is_admin' => false
        ]);

        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'is_admin' => true
        ]);
    }
}
