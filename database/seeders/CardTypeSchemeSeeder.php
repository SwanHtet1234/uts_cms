<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CardTypeScheme;

class CardTypeSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $combinations = [
            ['type_id' => 1, 'scheme_id' => 1], // Credit + MasterCard
            ['type_id' => 1, 'scheme_id' => 2], // Credit + Visa
            ['type_id' => 2, 'scheme_id' => 1], // Debit + MasterCard
            ['type_id' => 2, 'scheme_id' => 2], // Debit + Visa
            ['type_id' => 3, 'scheme_id' => 3], // ATM + American Express
        ];

        foreach ($combinations as $combination) {
            CardTypeScheme::create($combination);
        }
    }
}
