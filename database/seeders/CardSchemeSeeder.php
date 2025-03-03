<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CardScheme;

class CardSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemes = ['MasterCard', 'Visa', 'American Express'];

        foreach ($schemes as $scheme) {
            CardScheme::create(['scheme_name' => $scheme]);
        }
    }
}
