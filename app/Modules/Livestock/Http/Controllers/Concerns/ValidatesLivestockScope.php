<?php

namespace App\Modules\Livestock\Http\Controllers\Concerns;

use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockBreed;
use Illuminate\Validation\Validator;

trait ValidatesLivestockScope
{
    protected function addAnimalScopeValidation(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $data = $validator->getData();

            if (! empty($data['paddock_id']) && ! $this->idBelongsToFarm('paddocks', (int) $data['paddock_id'], (int) ($data['farm_id'] ?? 0))) {
                $validator->errors()->add('paddock_id', 'The selected paddock must belong to the selected farm.');
            }

            if (! empty($data['breed_id'])) {
                $breed = LivestockBreed::query()->find($data['breed_id']);
                if (! $breed || (int) $breed->species_id !== (int) ($data['species_id'] ?? 0)) {
                    $validator->errors()->add('breed_id', 'The selected breed must belong to the selected species.');
                }
            }

            foreach (['dam_id', 'sire_id'] as $key) {
                if (! empty($data[$key])) {
                    $relative = LivestockAnimal::query()->find($data[$key]);
                    if (! $relative || (int) $relative->organization_id !== (int) ($data['organization_id'] ?? 0) || (int) $relative->farm_id !== (int) ($data['farm_id'] ?? 0)) {
                        $validator->errors()->add($key, 'The selected parent animal must belong to the selected organization and farm.');
                    }
                }
            }
        });
    }

    protected function addGroupScopeValidation(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $data = $validator->getData();

            if (! empty($data['paddock_id']) && ! $this->idBelongsToFarm('paddocks', (int) $data['paddock_id'], (int) ($data['farm_id'] ?? 0))) {
                $validator->errors()->add('paddock_id', 'The selected paddock must belong to the selected farm.');
            }

            if (! empty($data['breed_id'])) {
                $breed = LivestockBreed::query()->find($data['breed_id']);
                if (! $breed || (int) $breed->species_id !== (int) ($data['species_id'] ?? 0)) {
                    $validator->errors()->add('breed_id', 'The selected breed must belong to the selected species.');
                }
            }
        });
    }

    protected function addProductOrganizationValidation(Validator $validator, int $organizationId): void
    {
        $validator->after(function (Validator $validator) use ($organizationId): void {
            $productId = $validator->getData()['product_id'] ?? null;
            if (! $productId) {
                return;
            }

            $product = InventoryProduct::query()->find($productId);
            if (! $product || (int) $product->organization_id !== $organizationId) {
                $validator->errors()->add('product_id', 'The selected product must belong to the same organization.');
            }
        });
    }

    protected function addMovementPaddockValidation(Validator $validator, int $farmId): void
    {
        $validator->after(function (Validator $validator) use ($farmId): void {
            foreach (['from_paddock_id', 'to_paddock_id'] as $key) {
                $paddockId = $validator->getData()[$key] ?? null;
                if ($paddockId && ! $this->idBelongsToFarm('paddocks', (int) $paddockId, $farmId)) {
                    $validator->errors()->add($key, 'The selected paddock must belong to the same farm.');
                }
            }
        });
    }

    protected function addGroupMortalityValidation(Validator $validator, LivestockAnimalGroup $group): void
    {
        $validator->after(function (Validator $validator) use ($group): void {
            $numberDead = (int) ($validator->getData()['number_dead'] ?? 0);
            if ($numberDead > $group->current_count) {
                $validator->errors()->add('number_dead', 'Group mortality cannot exceed the current count.');
            }
        });
    }

    protected function idBelongsToFarm(string $table, int $id, int $farmId): bool
    {
        return \DB::table($table)->where('id', $id)->where('farm_id', $farmId)->exists();
    }
}
