<?php

namespace App\Modules\Reports\Services;

use App\Modules\Assets\Models\Asset;
use App\Modules\Core\Models\Farm;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Sales\Models\SalesCustomer;

class ReportLabelResolver
{
    public function target(?string $type, mixed $id): string
    {
        if (! $type) {
            return 'General / Unallocated';
        }

        if ($id === null || $id === '') {
            return match ($type) {
                'farm_overhead' => 'Farm Overhead',
                'general' => 'General / Unallocated',
                default => str_replace('_', ' ', $type),
            };
        }

        return match ($type) {
            'crop_cycle' => $this->cropCycle((int) $id),
            'livestock_animal' => $this->animal((int) $id),
            'livestock_group', 'livestock_animal_group' => $this->animalGroup((int) $id),
            'asset' => $this->asset((int) $id),
            'farm', 'farm_overhead' => $this->farm((int) $id),
            'customer', 'sales_customer' => $this->customer((int) $id),
            default => $type.' #'.$id,
        };
    }

    private function cropCycle(int $id): string
    {
        $cycle = CropCycle::query()->with(['crop', 'field'])->find($id);

        return $cycle ? trim($cycle->name.' - '.($cycle->field?->name ?? $cycle->crop?->name ?? '')) : 'crop_cycle #'.$id;
    }

    private function animal(int $id): string
    {
        $animal = LivestockAnimal::query()->find($id);

        return $animal ? ($animal->name ?: $animal->tag_number ?: $animal->animal_code ?: 'Animal #'.$id) : 'livestock_animal #'.$id;
    }

    private function animalGroup(int $id): string
    {
        $group = LivestockAnimalGroup::query()->find($id);

        return $group ? ($group->name ?: $group->group_code ?: 'Group #'.$id) : 'livestock_group #'.$id;
    }

    private function asset(int $id): string
    {
        $asset = Asset::query()->find($id);

        return $asset ? ($asset->name ?: $asset->asset_code ?: 'Asset #'.$id) : 'asset #'.$id;
    }

    private function farm(int $id): string
    {
        return Farm::query()->find($id)?->name ?? 'farm #'.$id;
    }

    private function customer(int $id): string
    {
        return SalesCustomer::query()->find($id)?->name ?? 'customer #'.$id;
    }
}
