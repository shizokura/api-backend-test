<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'role' => 'admin',
                'email' => 'admin@example.com',
                'password' => 'water123'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
