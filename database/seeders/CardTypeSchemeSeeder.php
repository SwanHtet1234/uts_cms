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
        for ($typeId = 1; $typeId <= 6; $typeId++) {
            for ($schemeId = 1; $schemeId <= 4; $schemeId++) {
                $imageNumber = ($typeId - 1) * 4 + $schemeId;
                CardTypeScheme::create([
                    'type_id' => $typeId,
                    'scheme_id' => $schemeId,
                    'image' => 'card_type_scheme_images/' . $imageNumber . '.png',
                ]);
            }
        }
    }
}
