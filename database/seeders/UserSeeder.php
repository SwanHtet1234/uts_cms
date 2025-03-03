<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'username' => 'user' . $i,
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'phone' => '123456789' . $i,
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
