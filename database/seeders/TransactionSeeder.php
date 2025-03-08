<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\TransactionType;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = Card::all();
        $transactionTypes = TransactionType::pluck('id')->toArray();

        foreach ($cards as $card) {
            $numberOfTransactions = rand(20, 30);

            for ($i = 1; $i <= $numberOfTransactions; $i++) {
                Transaction::create([
                    'card_id' => $card->id,
                    'amount' => rand(100, 10000) / 100,
                    'datetime' => now()->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
                    'type' => $transactionTypes[array_rand($transactionTypes)],
                ]);
            }
        }
    }
}
