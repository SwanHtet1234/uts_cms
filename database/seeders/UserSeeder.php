<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize Faker
        $faker = Faker::create();

        $username = array('sbalistreri', 'scottie43', 'xborer', 'kunze.solon', 'arlene.steuber', 'jasper.koss', 'brielle73', 'ellis.hilpert', 'breana87', 'fturcotte');

        // Create 10 users
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'username' => $username[($i-1)],
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'), // Default password for all users
                'phone' => $faker->unique()->phoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
