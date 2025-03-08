<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Card;
use App\Models\CardService;
use App\Models\Service;

class CardServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = Card::all();
        $services = Service::pluck('id')->toArray();

        foreach ($cards as $card) {
            for ($i = 0; $i < 3; $i++) {
                CardService::create([
                    'card_id' => $card->id,
                    'service_id' => $services[$i],
                    'status' => true,
                    'spendingLimit' => rand(1000, 10000) / 100,
                    'globalOrLocal' => 'local',
                ]);

                CardService::create([
                    'card_id' => $card->id,
                    'service_id' => $services[$i],
                    'status' => true,
                    'spendingLimit' => rand(1000, 10000) / 100,
                    'globalOrLocal' => 'global',
                ]);
            }
        }
    }
}
