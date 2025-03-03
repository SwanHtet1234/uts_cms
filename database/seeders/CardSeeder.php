<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Card;
use App\Models\User;
use App\Models\CardTypeScheme;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $typeSchemes = CardTypeScheme::all();

        foreach ($users as $user) {
            $numberOfCards = rand(1, 5); // Each user has 1 to 5 cards

            for ($i = 1; $i <= $numberOfCards; $i++) {
                $typeScheme = $typeSchemes->random();

                Card::create([
                    'user_id' => $user->id,
                    'card_number' => $this->generateCardNumber(),
                    'expire_date' => now()->addYears(rand(1, 5))->format('Y-m-d'),
                    'cvv' => rand(100, 999),
                    'balance' => rand(100, 10000),
                    'type_scheme_id' => $typeScheme->id,
                    'status' => true,
                ]);
            }
        }
    }

    private function generateCardNumber()
    {
        return str_pad(rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
    }
}
