<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\CropCycle;
use App\Models\Field;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Seeder;

class CropCycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@smartshamba.com')->first();

        $farm1 = Farm::where('name', 'Green Valley Farm')->first();
        $farm2 = Farm::where('name', 'Sunrise Plantation')->first();
        $farm3 = Farm::where('name', 'Highland Organics')->first();

        $field1 = Field::where('name', 'North Field')->first();
        $field2 = Field::where('name', 'South Field')->first();
        $field3 = Field::where('name', 'Mango Orchard')->first();
        $field4 = Field::where('name', 'Banana Grove')->first();
        $field5 = Field::where('name', 'Organic Vegetables')->first();

        $crop1 = Crop::where('name', 'Tomatoes')->first();
        $crop2 = Crop::where('name', 'Maize')->first();
        $crop3 = Crop::where('name', 'Mangoes')->first();
        $crop4 = Crop::where('name', 'Bananas')->first();
        $crop5 = Crop::where('name', 'Mixed Vegetables')->first();

        // Tomato crop cycle - active, in vegetative stage
        CropCycle::firstOrCreate(
            ['code' => 'TOM-202601'],
            [
                'user_id' => $admin->id,
                'crop_id' => $crop1->id,
                'farm_id' => $farm1->id,
                'field_id' => $field1->id,
                'crop_name' => 'Tomatoes',
                'category' => 'Vegetables',
                'variety' => 'Roma',
                'season' => 'rain-fed',
                'start_date' => '2026-03-01',
                'expected_harvest_date' => '2026-06-15',
                'current_stage' => 'vegetative',
                'status' => 'active',
                'area_planted' => 2.5,
                'planting_method' => 'transplanting',
                'seed_quantity' => 5,
                'seedling_quantity' => 5000,
                'spacing_row' => 75,
                'spacing_plant' => 45,
                'plant_population' => 29600,
                'germination_rate' => 85,
                'survival_rate' => 90,
                'planting_labor_workers' => 10,
                'planting_labor_cost' => 15000,
                'prep_labor_workers' => 5,
                'prep_labor_hours' => 40,
                'prep_labor_cost' => 8000,
                'irrigation_type' => 'drip',
                'irrigation_schedule' => 'Every 2 days, 6am-8am',
                'drainage' => 'Good',
                'soil_type_override' => 'Loamy',
                'ph_level' => 6.2,
            ]
        );

        // Maize crop cycle - completed
        CropCycle::firstOrCreate(
            ['code' => 'MAI-202501'],
            [
                'user_id' => $admin->id,
                'crop_id' => $crop2->id,
                'farm_id' => $farm1->id,
                'field_id' => $field2->id,
                'crop_name' => 'Maize',
                'category' => 'Cereals',
                'variety' => 'H614',
                'season' => 'rain-fed',
                'start_date' => '2025-03-01',
                'expected_harvest_date' => '2025-08-01',
                'current_stage' => 'completed',
                'status' => 'completed',
                'area_planted' => 5.0,
                'planting_method' => 'direct_seeding',
                'seed_quantity' => 25,
                'seedling_quantity' => 0,
                'spacing_row' => 75,
                'spacing_plant' => 25,
                'plant_population' => 26667,
                'germination_rate' => 90,
                'survival_rate' => 88,
                'planting_labor_workers' => 15,
                'planting_labor_cost' => 22500,
                'prep_labor_workers' => 8,
                'prep_labor_hours' => 60,
                'prep_labor_cost' => 12000,
                'irrigation_type' => 'furrow',
                'drainage' => 'Fair',
                'soil_type_override' => 'Clay Loam',
                'ph_level' => 5.8,
            ]
        );

        // Mango crop cycle - planned
        CropCycle::firstOrCreate(
            ['code' => 'MAN-202602'],
            [
                'user_id' => $admin->id,
                'crop_id' => $crop3->id,
                'farm_id' => $farm2->id,
                'field_id' => $field3->id,
                'crop_name' => 'Mangoes',
                'category' => 'Fruits',
                'variety' => 'Kent',
                'season' => 'irrigated',
                'start_date' => '2026-05-01',
                'expected_harvest_date' => '2026-12-01',
                'current_stage' => 'planning',
                'status' => 'planned',
                'area_planted' => 3.0,
                'planting_method' => 'transplanting',
                'irrigation_type' => 'sprinkler',
                'soil_type_override' => 'Sandy Loam',
                'ph_level' => 6.5,
            ]
        );

        // Banana crop cycle - active, flowering stage
        CropCycle::firstOrCreate(
            ['code' => 'BAN-202502'],
            [
                'user_id' => $admin->id,
                'crop_id' => $crop4->id,
                'farm_id' => $farm2->id,
                'field_id' => $field4->id,
                'crop_name' => 'Bananas',
                'category' => 'Fruits',
                'variety' => 'Cavendish',
                'season' => 'year_round',
                'start_date' => '2025-06-15',
                'expected_harvest_date' => '2026-06-15',
                'current_stage' => 'flowering',
                'status' => 'active',
                'area_planted' => 2.0,
                'planting_method' => 'cuttings',
                'seedling_quantity' => 200,
                'spacing_row' => 300,
                'spacing_plant' => 300,
                'plant_population' => 2222,
                'survival_rate' => 95,
                'planting_labor_workers' => 8,
                'planting_labor_cost' => 12000,
                'prep_labor_workers' => 4,
                'prep_labor_hours' => 30,
                'prep_labor_cost' => 6000,
                'irrigation_type' => 'drip',
                'soil_type_override' => 'Volcanic Loam',
                'ph_level' => 6.0,
            ]
        );

        // Mixed Vegetables crop cycle - active
        CropCycle::firstOrCreate(
            ['code' => 'MIX-202603'],
            [
                'user_id' => $admin->id,
                'crop_id' => $crop5->id,
                'farm_id' => $farm3->id,
                'field_id' => $field5->id,
                'crop_name' => 'Mixed Vegetables',
                'category' => 'Vegetables',
                'variety' => 'Various',
                'season' => 'rain-fed',
                'start_date' => '2026-02-01',
                'expected_harvest_date' => '2026-05-15',
                'current_stage' => 'fruiting',
                'status' => 'active',
                'area_planted' => 4.0,
                'planting_method' => 'direct_seeding',
                'seed_quantity' => 8,
                'spacing_row' => 50,
                'spacing_plant' => 30,
                'plant_population' => 66667,
                'germination_rate' => 88,
                'survival_rate' => 92,
                'planting_labor_workers' => 12,
                'planting_labor_cost' => 18000,
                'prep_labor_workers' => 6,
                'prep_labor_hours' => 48,
                'prep_labor_cost' => 9600,
                'irrigation_type' => 'manual',
                'drainage' => 'Good',
                'soil_type_override' => 'Dark Loam',
                'ph_level' => 6.8,
            ]
        );
    }
}