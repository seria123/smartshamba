<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropActivity;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropSeason;
use App\Modules\Crops\Models\CropVariety;
use Illuminate\Database\Seeder;

class CropsModuleSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $field = Field::query()->where('farm_id', $farm?->id)->first();

        if (! $organization || ! $farm || ! $field) {
            return;
        }

        $crop = Crop::query()->updateOrCreate(
            ['organization_id' => null, 'code' => 'TOMATO'],
            ['name' => 'Tomato', 'crop_type' => 'vegetable', 'scientific_name' => 'Solanum lycopersicum', 'default_growing_days' => 90, 'status' => 'active'],
        );

        $variety = CropVariety::query()->updateOrCreate(
            ['crop_id' => $crop->id, 'code' => 'TOM-DEMO'],
            ['name' => 'Demo Tomato Variety', 'expected_growing_days' => 85, 'seed_rate' => 0.25, 'seed_rate_unit' => 'kg/acre', 'status' => 'active'],
        );

        $season = CropSeason::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'code' => 'DEMO-2026-A'],
            ['farm_id' => $farm->id, 'name' => 'Demo 2026 A Season', 'start_date' => now()->toDateString(), 'end_date' => now()->addMonths(4)->toDateString(), 'season_type' => 'main', 'status' => 'active'],
        );

        $cycle = CropCycle::query()->updateOrCreate(
            ['cycle_number' => 'CYCLE-2026-0001'],
            [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'field_id' => $field->id,
                'crop_id' => $crop->id,
                'variety_id' => $variety->id,
                'season_id' => $season->id,
                'name' => 'Demo Tomato North Field',
                'planned_start_date' => now()->toDateString(),
                'actual_planting_date' => now()->toDateString(),
                'expected_harvest_date' => now()->addDays(85)->toDateString(),
                'area_planted' => 1.5,
                'area_unit' => 'acres',
                'plant_population' => 4500,
                'spacing' => '60cm x 45cm',
                'seed_source' => 'Demo seed lot, no stock deduction.',
                'status' => 'planted',
            ],
        );

        CropActivity::query()->updateOrCreate(
            ['crop_cycle_id' => $cycle->id, 'activity_type' => 'planting', 'activity_date' => now()->toDateString()],
            ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'field_id' => $field->id, 'status' => 'recorded', 'notes' => 'Local/testing demo crop activity.'],
        );
    }
}
