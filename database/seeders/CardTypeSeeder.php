<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CardType;

class CardTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['ATM', 'Credit', 'Debit', 'Prepaid', 'Virtual', 'Business'];

        foreach ($types as $type) {
            CardType::create(['type' => $type]);
        }
    }
}
