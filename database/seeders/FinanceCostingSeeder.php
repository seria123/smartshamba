<?php

namespace Database\Seeders;

use App\Modules\Assets\Models\Asset;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Finance\Models\FinanceCostCategory;
use App\Modules\Finance\Models\FinanceCostCentre;
use App\Modules\Finance\Services\CostEntryService;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use Illuminate\Database\Seeder;

class FinanceCostingSeeder extends Seeder
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

        $categories = [
            ['Seeds & Planting Material', 'variable', 'crop'],
            ['Fertiliser & Soil Amendments', 'variable', 'crop'],
            ['Crop Protection Chemicals', 'variable', 'crop'],
            ['Labour', 'variable', 'labour'],
            ['Machinery & Equipment', 'capital', 'asset'],
            ['Irrigation & Water', 'variable', 'irrigation'],
            ['Fuel & Energy', 'variable', 'asset'],
            ['Animal Feed', 'variable', 'livestock'],
            ['Veterinary & Medicines', 'variable', 'livestock'],
            ['Breeding', 'variable', 'livestock'],
            ['Maintenance & Repairs', 'fixed', 'maintenance'],
            ['Transport', 'variable', 'other'],
            ['Packaging', 'variable', 'inventory'],
            ['General Farm Overheads', 'overhead', 'manual'],
            ['Other', 'other', 'other'],
        ];

        foreach ($categories as $index => [$name, $nature, $source]) {
            FinanceCostCategory::query()->firstOrCreate(
                ['organization_id' => $organization->id, 'farm_id' => null, 'name' => $name],
                ['cost_nature' => $nature, 'default_source_module' => $source, 'is_active' => true, 'sort_order' => $index + 1],
            );
        }

        $adminCentre = FinanceCostCentre::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Farm Administration'], ['code' => 'ADMIN', 'centre_type' => 'admin', 'is_active' => true]);
        $cropCentre = FinanceCostCentre::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Crop Production'], ['code' => 'CROP', 'centre_type' => 'crop', 'is_active' => true]);
        $livestockCentre = FinanceCostCentre::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Livestock Unit'], ['code' => 'LIVE', 'centre_type' => 'livestock', 'is_active' => true]);

        $service = app(CostEntryService::class);
        $overhead = FinanceCostCategory::where('name', 'General Farm Overheads')->firstOrFail();
        $seed = FinanceCostCategory::where('name', 'Seeds & Planting Material')->firstOrFail();
        $feed = FinanceCostCategory::where('name', 'Animal Feed')->firstOrFail();
        $maintenance = FinanceCostCategory::where('name', 'Maintenance & Repairs')->firstOrFail();

        $this->firstEntry($service, $organization->id, $farm->id, $overhead->id, $adminCentre->id, 'Farm office utilities', 12500, 'confirmed', 'general_farm', 'Farm overhead');
        $this->firstEntry($service, $organization->id, $farm->id, $seed->id, $cropCentre->id, 'Demo seed purchase costing', 18400, 'confirmed', 'crop_cycle', CropCycle::query()->where('farm_id', $farm->id)->value('cycle_code') ?: 'Crop cycle');
        $this->firstEntry($service, $organization->id, $farm->id, $feed->id, $livestockCentre->id, 'Demo feed costing', 9600, 'confirmed', 'livestock_group', LivestockAnimalGroup::query()->where('farm_id', $farm->id)->value('name') ?: 'Livestock group');
        $this->firstEntry($service, $organization->id, $farm->id, $maintenance->id, $adminCentre->id, 'Demo asset maintenance costing', 7200, 'draft', 'asset', Asset::query()->where('farm_id', $farm->id)->value('name') ?: 'Asset');
    }

    private function firstEntry(CostEntryService $service, int $organizationId, int $farmId, int $categoryId, int $centreId, string $title, float $amount, string $status, string $allocationType, string $label): void
    {
        if (\App\Modules\Finance\Models\FinanceCostEntry::query()->where('organization_id', $organizationId)->where('title', $title)->exists()) {
            return;
        }

        $entry = $service->create([
            'organization_id' => $organizationId,
            'farm_id' => $farmId,
            'cost_category_id' => $categoryId,
            'cost_centre_id' => $centreId,
            'entry_date' => today()->toDateString(),
            'title' => $title,
            'source_module' => 'manual',
            'amount' => $amount,
            'currency' => 'KES',
            'payment_state' => 'not_tracked',
            'allocation_type' => $allocationType,
            'allocation_label' => $label,
            'allocation_amount' => $amount,
        ]);

        if ($status === 'confirmed') {
            $service->confirm($entry, \App\Models\User::query()->value('id') ?? 1);
        }
    }
}
