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
        $cardTypeSchemes = range(1, 24);

        foreach ($users as $user) {
            $numberOfCards = rand(1, 7);

            for ($i = 1; $i <= $numberOfCards; $i++) {
                Card::create([
                    'user_id' => $user->id,
                    'cardNumber' => $this->generateCardNumber(),
                    'expireDate' => now()->addYears(rand(1, 5))->format('Y-m-d'),
                    'cvv' => rand(100, 999),
                    'balance' => rand(1000, 100000) / 100,
                    'type_scheme_id' => $cardTypeSchemes[array_rand($cardTypeSchemes)],
                    'status' => 'active',
                ]);
            }
        }
    }

    private function generateCardNumber(): string
    {
        return implode('', array_map(fn () => rand(0, 9), array_fill(0, 16, 0)));
    }
}
