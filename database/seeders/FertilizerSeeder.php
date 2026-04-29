<?php

namespace Database\Seeders;

use App\Models\Fertilizer;
use App\Models\FertilizerType;
use Illuminate\Database\Seeder;

class FertilizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get fertilizer types
        $ureaType = FertilizerType::where('name', 'Urea')->first();
        $dapType = FertilizerType::where('name', 'DAP (Diammonium Phosphate)')->first();
        $mopType = FertilizerType::where('name', 'MOP (Muriate of Potash)')->first();
        $npkType = FertilizerType::where('name', 'NPK 17:17:17')->first();
        $compostType = FertilizerType::where('name', 'Organic Compost')->first();

        $fertilizers = [
            [
                'name' => 'Urea Premium',
                'fertilizer_type_id' => $ureaType->id,
                'quantity' => 500,
                'unit' => 'kg',
                'unit_cost' => 0.50,
                'expiry_date' => now()->addYears(2),
                'is_active' => true,
            ],
            [
                'name' => 'DAP Standard',
                'fertilizer_type_id' => $dapType->id,
                'quantity' => 300,
                'unit' => 'kg',
                'unit_cost' => 0.60,
                'expiry_date' => now()->addYears(2),
                'is_active' => true,
            ],
            [
                'name' => 'MOP Granular',
                'fertilizer_type_id' => $mopType->id,
                'quantity' => 200,
                'unit' => 'kg',
                'unit_cost' => 0.40,
                'expiry_date' => now()->addYears(2),
                'is_active' => true,
            ],
            [
                'name' => 'NPK 17:17:17',
                'fertilizer_type_id' => $npkType->id,
                'quantity' => 400,
                'unit' => 'kg',
                'unit_cost' => 0.55,
                'expiry_date' => now()->addYears(2),
                'is_active' => true,
            ],
            [
                'name' => 'Organic Compost',
                'fertilizer_type_id' => $compostType->id,
                'quantity' => 1000,
                'unit' => 'kg',
                'unit_cost' => 0.25,
                'expiry_date' => now()->addYears(1),
                'is_active' => true,
            ],
        ];

        foreach ($fertilizers as $fertilizer) {
            Fertilizer::updateOrCreate(
                ['name' => $fertilizer['name']],
                $fertilizer
            );
        }
    }
}