<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\CropRotation;
use App\Models\CropSeason;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    public function run(): void
    {
        $crops = [
            [
                'name' => 'Maize',
                'category' => 'grain',
                'description' => 'Common maize (corn) - staple food crop',
                'variety' => 'Composite Hybrid H614',
                'days_to_maturity' => 120,
                'average_yield_per_hectare' => 6000,
                'yield_unit' => 'kg',
                'season_type' => 'long_rain',
                'growth_stages' => ['germination', 'vegetative', 'tasseling', 'grain_fill', 'maturity'],
                'soil_requirements' => ['type' => 'loamy', 'ph_range' => '5.8-7.0'],
                'water_requirements' => ['mm_per_season' => 500, 'critical_stages' => ['tasseling', 'grain_fill']],
                'min_temperature' => 15,
                'max_temperature' => 35,
                'seasons' => [
                    ['name' => 'Masika (Long Rain)', 'season_period' => 'long_rain', 'planting_start' => '2025-03-01', 'planting_end' => '2025-04-15', 'harvest_start' => '2025-07-01', 'harvest_end' => '2025-09-15'],
                    ['name' => 'Vuli (Short Rain)', 'season_period' => 'short_rain', 'planting_start' => '2025-10-15', 'planting_end' => '2025-11-30', 'harvest_start' => '2026-02-01', 'harvest_end' => '2026-04-30'],
                ],
            ],
            [
                'name' => 'Rice',
                'category' => 'grain',
                'description' => 'Paddy rice - irrigated or rain-fed',
                'variety' => 'SARO 5',
                'days_to_maturity' => 130,
                'average_yield_per_hectare' => 4500,
                'yield_unit' => 'kg',
                'season_type' => 'all_season',
                'growth_stages' => ['germination', 'seedling', 'tillering', 'panicle_init', 'flowering', 'grain_fill'],
                'soil_requirements' => ['type' => 'clay', 'ph_range' => '5.5-7.0'],
                'water_requirements' => ['mm_per_season' => 1200, 'flooded' => true],
                'min_temperature' => 20,
                'max_temperature' => 35,
                'seasons' => [],
            ],
            [
                'name' => 'Beans',
                'category' => 'legume',
                'description' => 'Common beans - protein-rich legume',
                'variety' => 'SELIAN RO-7',
                'days_to_maturity' => 75,
                'average_yield_per_hectare' => 1500,
                'yield_unit' => 'kg',
                'season_type' => 'short_rain',
                'growth_stages' => ['germination', 'vegetative', 'flowering', 'pod_dev', 'maturity'],
                'soil_requirements' => ['type' => 'loamy', 'ph_range' => '6.0-7.0'],
                'water_requirements' => ['mm_per_season' => 400],
                'min_temperature' => 15,
                'max_temperature' => 25,
                'seasons' => [
                    ['name' => 'Vuli (Short Rain)', 'season_period' => 'short_rain', 'planting_start' => '2025-10-01', 'planting_end' => '2025-11-15', 'harvest_start' => '2025-12-15', 'harvest_end' => '2026-02-01'],
                ],
            ],
            [
                'name' => 'Tomatoes',
                'category' => 'vegetable',
                'description' => 'Fresh market tomatoes',
                'variety' => 'Roma VF',
                'days_to_maturity' => 90,
                'average_yield_per_hectare' => 25000,
                'yield_unit' => 'kg',
                'season_type' => 'all_season',
                'growth_stages' => ['seedling', 'vegetative', 'flowering', 'fruit_set', 'fruit_dev', 'ripening'],
                'soil_requirements' => ['type' => 'sandy_loam', 'ph_range' => '6.0-6.5'],
                'water_requirements' => ['mm_per_season' => 600, 'drip_irrigation' => true],
                'min_temperature' => 15,
                'max_temperature' => 30,
                'seasons' => [],
            ],
            [
                'name' => 'Cassava',
                'category' => 'tubers',
                'description' => 'Staple root crop - drought tolerant',
                'variety' => 'Kigoma',
                'days_to_maturity' => 365,
                'average_yield_per_hectare' => 20000,
                'yield_unit' => 'kg',
                'season_type' => 'all_season',
                'growth_stages' => ['sprouting', 'vegetative', 'tuber_init', 'tuber_fill'],
                'soil_requirements' => ['type' => 'sandy', 'ph_range' => '5.5-7.0'],
                'water_requirements' => ['mm_per_season' => 800],
                'min_temperature' => 18,
                'max_temperature' => 35,
                'seasons' => [],
            ],
            [
                'name' => 'Mangoes',
                'category' => 'fruit',
                'description' => 'Tropical fruit - Kent variety',
                'variety' => 'Kent',
                'days_to_maturity' => 1080,
                'average_yield_per_hectare' => 15000,
                'yield_unit' => 'kg',
                'season_type' => 'long_rain',
                'growth_stages' => ['vegetative', 'flowering', 'fruit_set', 'fruit_dev', 'ripening'],
                'soil_requirements' => ['type' => 'deep_loam', 'ph_range' => '5.5-7.5'],
                'water_requirements' => ['mm_per_season' => 1000, 'dry_season' => true],
                'min_temperature' => 20,
                'max_temperature' => 40,
                'seasons' => [
                    ['name' => 'Mango Season', 'season_period' => 'long_rain', 'planting_start' => '2025-03-01', 'planting_end' => '2025-04-30', 'harvest_start' => '2025-11-01', 'harvest_end' => '2026-01-31'],
                ],
            ],
            [
                'name' => 'Bananas',
                'category' => 'fruit',
                'description' => 'Dessert bananas',
                'variety' => 'Cavendish',
                'days_to_maturity' => 365,
                'average_yield_per_hectare' => 30000,
                'yield_unit' => 'kg',
                'season_type' => 'all_season',
                'growth_stages' => ['vegetative', 'flowering', 'fruit_set', 'fruit_dev'],
                'soil_requirements' => ['type' => 'volcanic', 'ph_range' => '5.5-7.0'],
                'water_requirements' => ['mm_per_season' => 1500, 'consistent' => true],
                'min_temperature' => 15,
                'max_temperature' => 35,
                'seasons' => [],
            ],
            [
                'name' => 'Green grams',
                'category' => 'legume',
                'description' => 'Mung beans - short season legume',
                'variety' => 'N端正',
                'days_to_maturity' => 65,
                'average_yield_per_hectare' => 800,
                'yield_unit' => 'kg',
                'season_type' => 'short_rain',
                'growth_stages' => ['germination', 'vegetative', 'flowering', 'pod_dev'],
                'soil_requirements' => ['type' => 'sandy_loam', 'ph_range' => '6.0-7.5'],
                'water_requirements' => ['mm_per_season' => 300],
                'min_temperature' => 20,
                'max_temperature' => 35,
                'seasons' => [],
            ],
            [
                'name' => 'Sunflowers',
                'category' => 'grain',
                'description' => 'Oilseed crop',
                'variety' => 'H8998',
                'days_to_maturity' => 95,
                'average_yield_per_hectare' => 1800,
                'yield_unit' => 'kg',
                'season_type' => 'short_rain',
                'growth_stages' => ['germination', 'vegetative', 'flowering', 'seed_dev'],
                'soil_requirements' => ['type' => 'loamy', 'ph_range' => '6.0-7.5'],
                'water_requirements' => ['mm_per_season' => 400],
                'min_temperature' => 15,
                'max_temperature' => 30,
                'seasons' => [],
            ],
            [
                'name' => 'Groundnuts',
                'category' => 'legume',
                'description' => 'Peanuts - oilseed and food crop',
                'variety' => 'Red',
                'season_type' => 'short_rain',
                'days_to_maturity' => 120,
                'average_yield_per_hectare' => 1500,
                'yield_unit' => 'kg',
            ],
        ];

        foreach ($crops as $cropData) {
            $seasons = $cropData['seasons'] ?? [];
            unset($cropData['seasons']);

            $crop = Crop::updateOrCreate(
                ['name' => $cropData['name']],
                $cropData
            );

            foreach ($seasons as $season) {
                CropSeason::updateOrCreate(
                    ['crop_id' => $crop->id, 'name' => $season['name']],
                    [
                        'season_period' => $season['season_period'],
                        'planting_start_date' => $season['planting_start'],
                        'planting_end_date' => $season['planting_end'],
                        'expected_harvest_start' => $season['harvest_start'],
                        'expected_harvest_end' => $season['harvest_end'],
                        'is_optimal' => true,
                    ]
                );
            }
        }

        $rotations = [
            ['crop' => 'Maize', 'previous' => 'Beans', 'order' => 1, 'benefit' => 15.0, 'benefits' => 'Nitrogen fixation improves soil fertility'],
            ['crop' => 'Maize', 'previous' => 'Groundnuts', 'order' => 1, 'benefit' => 12.0, 'benefits' => 'Loosens soil structure'],
            ['crop' => 'Beans', 'previous' => 'Maize', 'order' => 1, 'benefit' => 8.0, 'benefits' => 'Residue provides organic matter'],
            ['crop' => 'Rice', 'previous' => 'Beans', 'order' => 1, 'benefit' => 10.0, 'benefits' => 'Green manure improves yield'],
            ['crop' => 'Tomatoes', 'previous' => 'Beans', 'order' => 1, 'benefit' => 5.0, 'benefits' => 'Nematode suppression'],
        ];

        foreach ($rotations as $rotation) {
            $crop = Crop::where('name', $rotation['crop'])->first();
            $previousCrop = Crop::where('name', $rotation['previous'])->first();

            if ($crop && $previousCrop) {
                CropRotation::updateOrCreate(
                    ['crop_id' => $crop->id, 'previous_crop_id' => $previousCrop->id],
                    [
                        'sequence_order' => $rotation['order'],
                        'yield_benefit_percentage' => $rotation['benefit'],
                        'benefits' => $rotation['benefits'],
                    ]
                );
            }
        }
    }
}
