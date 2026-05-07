<?php

namespace Database\Seeders;

use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Assets\Models\AssetMaintenanceRecord;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Assets\Models\AssetUsageRecord;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Irrigation\Models\IrrigationEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetsMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();

        if (! $organization || ! $farm) {
            return;
        }

        $categories = collect(['Pumps', 'Vehicles', 'Tractors', 'Sprayers', 'Generators', 'Tools', 'Irrigation Equipment', 'Storage Equipment'])
            ->mapWithKeys(fn (string $name) => [$name => AssetCategory::query()->firstOrCreate(['organization_id' => $organization->id, 'slug' => Str::slug($name)], ['name' => $name, 'is_system' => false, 'status' => 'active'])]);

        $pump = Asset::query()->firstOrCreate(['farm_id' => $farm->id, 'asset_code' => 'AST-PUMP-001'], ['organization_id' => $organization->id, 'category_id' => $categories['Pumps']->id, 'name' => 'Main Borehole Pump', 'asset_type' => 'pump', 'status' => 'active', 'condition_status' => 'good', 'acquisition_source' => 'purchased', 'next_service_date' => today()->addDays(14)]);
        Asset::query()->firstOrCreate(['farm_id' => $farm->id, 'asset_code' => 'AST-TRAC-001'], ['organization_id' => $organization->id, 'category_id' => $categories['Tractors']->id, 'name' => 'Farm Tractor 001', 'asset_type' => 'tractor', 'status' => 'active', 'condition_status' => 'good']);
        $sprayer = Asset::query()->firstOrCreate(['farm_id' => $farm->id, 'asset_code' => 'AST-SPR-001'], ['organization_id' => $organization->id, 'category_id' => $categories['Sprayers']->id, 'name' => 'Knapsack Sprayer 01', 'asset_type' => 'tool', 'status' => 'active', 'condition_status' => 'fair']);
        $generator = Asset::query()->firstOrCreate(['farm_id' => $farm->id, 'asset_code' => 'AST-GEN-001'], ['organization_id' => $organization->id, 'category_id' => $categories['Generators']->id, 'name' => 'Generator A', 'asset_type' => 'generator', 'status' => 'broken_down', 'condition_status' => 'poor']);
        Asset::query()->firstOrCreate(['farm_id' => $farm->id, 'asset_code' => 'AST-TANK-001'], ['organization_id' => $organization->id, 'category_id' => $categories['Storage Equipment']->id, 'name' => 'Water Tank A', 'asset_type' => 'storage_equipment', 'status' => 'active', 'condition_status' => 'good']);
        Asset::query()->firstOrCreate(['farm_id' => $farm->id, 'asset_code' => 'AST-PICKUP-001'], ['organization_id' => $organization->id, 'category_id' => $categories['Vehicles']->id, 'name' => 'Farm Pickup', 'asset_type' => 'vehicle', 'status' => 'active', 'condition_status' => 'fair']);

        AssetMaintenanceSchedule::query()->firstOrCreate(['schedule_number' => 'AMS-DEMO-001'], ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'asset_id' => $pump->id, 'maintenance_type' => 'routine_service', 'scheduled_date' => today()->addDays(7), 'frequency_type' => 'monthly', 'frequency_interval' => 1, 'priority' => 'normal', 'status' => 'planned', 'instructions' => 'Local/testing demo pump service.']);
        AssetMaintenanceRecord::query()->firstOrCreate(['record_number' => 'AMR-DEMO-001'], ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'asset_id' => $sprayer->id, 'maintenance_type' => 'cleaning', 'maintenance_date' => today(), 'status' => 'recorded', 'work_done' => 'Cleaned nozzle and checked seals.', 'product_id' => InventoryProduct::query()->where('organization_id', $organization->id)->value('id')]);
        AssetBreakdownRecord::query()->firstOrCreate(['breakdown_number' => 'ABD-DEMO-001'], ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'asset_id' => $generator->id, 'breakdown_date' => today(), 'issue_type' => 'engine_issue', 'severity' => 'high', 'status' => 'open', 'description' => 'Generator failed to start during local/testing demo.']);
        AssetUsageRecord::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'asset_id' => $pump->id, 'usage_date' => today(), 'usage_type' => 'irrigation'], ['related_irrigation_event_id' => IrrigationEvent::query()->where('farm_id', $farm->id)->value('id'), 'duration_minutes' => 55, 'notes' => 'Local/testing demo irrigation usage.']);
    }
}
