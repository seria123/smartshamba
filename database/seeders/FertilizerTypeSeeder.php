<?php

namespace Database\Seeders;

use App\Models\FertilizerType;
use Illuminate\Database\Seeder;

class FertilizerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fertilizerTypes = [
            [
                'name' => 'Urea',
                'description' => 'High nitrogen fertilizer (46-0-0)',
                'type' => 'nitrogen',
                'default_unit' => 'kg',
                'min_threshold' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'DAP (Diammonium Phosphate)',
                'description' => 'Phosphorus fertilizer (18-46-0)',
                'type' => 'phosphorus',
                'default_unit' => 'kg',
                'min_threshold' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'MOP (Muriate of Potash)',
                'description' => 'Potassium fertilizer (0-0-60)',
                'type' => 'potassium',
                'default_unit' => 'kg',
                'min_threshold' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'NPK 17:17:17',
                'description' => 'Balanced compound fertilizer',
                'type' => 'compound',
                'default_unit' => 'kg',
                'min_threshold' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Organic Compost',
                'description' => 'Well-decomposed organic matter',
                'type' => 'organic',
                'default_unit' => 'kg',
                'min_threshold' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($fertilizerTypes as $type) {
            FertilizerType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}