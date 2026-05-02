<?php

namespace Database\Seeders;

use App\Models\PlantingSchedule;
use App\Models\Crop;
use App\Models\Field;
use App\Models\Farm;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PlantingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $crops = Crop::all();
        $fields = Field::all();
        $farms = Farm::all();

        if ($crops->isEmpty() || $fields->isEmpty()) {
            $this->command->warn('No crops or fields found. Please seed crops and fields first.');
            return;
        }

        $seasons = ['spring', 'summer', 'fall', 'winter'];
        $statuses = ['planned', 'planted', 'growing', 'ready_for_harvest', 'harvested', 'cancelled'];
        $growthStages = ['planning', 'bed_preparation', 'sowing', 'germination', 'seedling', 'vegetative', 'flowering', 'fruiting', 'ripening', 'harvest_ready'];

        $today = Carbon::today();

        // Create 30 sample plantings
        for ($i = 0; $i < 30; $i++) {
            $crop = $crops->random();
            $field = $fields->random();
            $farm = $farms->isNotEmpty() ? $farms->random() : null;
            
            // Random date within next 6 months
            $plantingDate = $today->copy()->addDays(rand(-30, 180));
            $expectedHarvest = $plantingDate->copy()->addDays(rand(60, 180));
            
            $status = $statuses[array_rand($statuses)];
            
            // Adjust harvest date if harvested
            if ($status === 'harvested') {
                $actualHarvest = $expectedHarvest->copy()->addDays(rand(-7, 14));
            } else {
                $actualHarvest = null;
            }
            
            // Determine completion percentage based on status
            $completion = match($status) {
                'planned' => 0,
                'planted' => rand(5, 15),
                'growing' => rand(20, 70),
                'ready_for_harvest' => rand(85, 99),
                'harvested' => 100,
                'cancelled' => 0,
            };
            
            // Determine season based on month
            $month = $plantingDate->month;
            $season = match($month) {
                3, 4, 5 => 'spring',
                6, 7, 8 => 'summer',
                9, 10, 11 => 'fall',
                12, 1, 2 => 'winter',
            };

            PlantingSchedule::create([
                'user_id' => 1, // Adjust as needed
                'crop_id' => $crop->id,
                'field_id' => $field->id,
                'farm_id' => $farm?->id,
                'planting_date' => $plantingDate,
                'planting_window_start' => $plantingDate->copy()->subDays(rand(0, 7)),
                'planting_window_end' => $plantingDate->copy()->addDays(rand(0, 7)),
                'expected_harvest_date' => $expectedHarvest,
                'actual_harvest_date' => $actualHarvest,
                'estimated_quantity' => rand(100, 5000),
                'quantity_unit' => 'kg',
                'actual_quantity' => $status === 'harvested' ? rand(80, 5000) : null,
                'actual_quantity_unit' => $status === 'harvested' ? 'kg' : null,
                'status' => $status,
                'season' => $season,
                'variety' => rand(0, 1) ? $crop->variety : null,
                'notes' => $i % 3 === 0 ? 'Sample planting note: Monitor for pests regularly.' : null,
                'completion_percentage' => $completion,
                'current_stage' => $growthStages[array_rand($growthStages)],
            ]);
        }

        $this->command->info('Created 30 sample planting schedules.');
    }
}
