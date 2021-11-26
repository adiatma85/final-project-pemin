<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert User
        $admin1 = [
            'name' => 'Administrator',
            'email' => 'admin@localhost',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ];

        $admin2 = [
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ];

        $user1 = [
            'name' => 'User1',
            'email' => 'user1@admin.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ];

        $user2 = [
            'name' => 'User2',
            'email' => 'user2@admin.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ];

        User::insert($admin1);
        User::insert($admin2);
        User::insert($user1);
        User::insert($user2);
    }
}
