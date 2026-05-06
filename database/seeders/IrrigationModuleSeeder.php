<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Site;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Irrigation\Models\IrrigationSchedule;
use App\Modules\Irrigation\Models\IrrigationWaterReading;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use Illuminate\Database\Seeder;

class IrrigationModuleSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $site = Site::query()->where('farm_id', $farm?->id)->first();
        $field = Field::query()->where('farm_id', $farm?->id)->first();

        if (! $organization || ! $farm || ! $field) {
            return;
        }

        $borehole = IrrigationWaterSource::query()->updateOrCreate(['farm_id' => $farm->id, 'code' => 'BH-MAIN'], ['organization_id' => $organization->id, 'name' => 'Main Borehole', 'source_type' => 'borehole', 'capacity' => 50000, 'capacity_unit' => 'litres', 'status' => 'active']);
        IrrigationWaterSource::query()->updateOrCreate(['farm_id' => $farm->id, 'code' => 'RIVER-INTAKE'], ['organization_id' => $organization->id, 'name' => 'River Intake', 'source_type' => 'river', 'status' => 'active']);
        IrrigationWaterSource::query()->updateOrCreate(['farm_id' => $farm->id, 'code' => 'TANK-A'], ['organization_id' => $organization->id, 'name' => 'Storage Tank A', 'source_type' => 'tank', 'capacity' => 10000, 'capacity_unit' => 'litres', 'status' => 'active']);

        $zone = IrrigationZone::query()->updateOrCreate(['farm_id' => $farm->id, 'code' => 'TOM-B-DRIP'], ['organization_id' => $organization->id, 'site_id' => $site?->id, 'field_id' => $field->id, 'water_source_id' => $borehole->id, 'name' => 'Tomato Block B Drip Zone', 'zone_type' => 'field_zone', 'irrigation_method' => 'drip', 'area' => 1.5, 'area_unit' => 'acres', 'status' => 'active']);
        IrrigationZone::query()->updateOrCreate(['farm_id' => $farm->id, 'code' => 'MAIZE-A-FURROW'], ['organization_id' => $organization->id, 'site_id' => $site?->id, 'field_id' => $field->id, 'water_source_id' => $borehole->id, 'name' => 'Maize Field A Furrow Zone', 'zone_type' => 'field_zone', 'irrigation_method' => 'furrow', 'status' => 'active']);
        IrrigationZone::query()->updateOrCreate(['farm_id' => $farm->id, 'code' => 'GH-1'], ['organization_id' => $organization->id, 'site_id' => $site?->id, 'water_source_id' => $borehole->id, 'name' => 'Greenhouse 1 Zone', 'zone_type' => 'greenhouse_zone', 'irrigation_method' => 'drip', 'status' => 'active']);

        $cycle = CropCycle::query()->where('farm_id', $farm->id)->first();
        $schedule = IrrigationSchedule::query()->firstOrCreate(['schedule_number' => 'IRR-SCH-DEMO-001'], ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'irrigation_zone_id' => $zone->id, 'field_id' => $field->id, 'crop_cycle_id' => $cycle?->id, 'scheduled_date' => today()->toDateString(), 'planned_duration_minutes' => 60, 'planned_water_volume' => 1500, 'water_volume_unit' => 'litres', 'priority' => 'normal', 'status' => 'planned', 'instructions' => 'Local/testing demo irrigation schedule.']);
        IrrigationEvent::query()->firstOrCreate(['event_number' => 'IRR-EVT-DEMO-001'], ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'irrigation_zone_id' => $zone->id, 'water_source_id' => $borehole->id, 'field_id' => $field->id, 'crop_cycle_id' => $cycle?->id, 'schedule_id' => $schedule->id, 'irrigation_date' => today()->toDateString(), 'duration_minutes' => 55, 'water_volume' => 1400, 'water_volume_unit' => 'litres', 'method' => 'drip', 'status' => 'recorded', 'notes' => 'Local/testing demo irrigation event.']);
        IrrigationWaterReading::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'water_source_id' => $borehole->id, 'reading_date' => today()->toDateString(), 'reading_type' => 'meter_reading'], ['value' => 12345, 'unit_of_measure' => 'litres']);
        IrrigationIssue::query()->firstOrCreate(['issue_number' => 'IRR-ISS-DEMO-001'], ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'irrigation_zone_id' => $zone->id, 'water_source_id' => $borehole->id, 'field_id' => $field->id, 'issue_date' => today()->toDateString(), 'issue_type' => 'low_pressure', 'severity' => 'medium', 'status' => 'open', 'description' => 'Low pressure in Tomato Block B.']);
    }
}
