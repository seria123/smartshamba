<?php

namespace App\Modules\Assets\Http\Controllers\Concerns;

use App\Modules\Assets\Models\Asset;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use Illuminate\Validation\Validator;

trait ValidatesAssetScope
{
    protected function addFarmScopedValidation(Validator $validator, array $fields): void
    {
        $validator->after(function (Validator $validator) use ($fields): void {
            $data = $validator->getData();
            $farmId = (int) ($data['farm_id'] ?? 0);

            foreach ($fields as $field => $table) {
                if (! empty($data[$field]) && ! $this->idBelongsToFarm($table, (int) $data[$field], $farmId)) {
                    $validator->errors()->add($field, 'The selected record must belong to the selected farm.');
                }
            }
        });
    }

    protected function addAssetScopeValidation(Validator $validator, string $field = 'asset_id'): void
    {
        $validator->after(function (Validator $validator) use ($field): void {
            $data = $validator->getData();
            if (! empty($data[$field]) && ! Asset::query()->where('id', $data[$field])->where('farm_id', $data['farm_id'] ?? 0)->exists()) {
                $validator->errors()->add($field, 'The selected asset must belong to the selected farm.');
            }
        });
    }

    protected function addUserScopeValidation(Validator $validator, string $field): void
    {
        $validator->after(function (Validator $validator) use ($field): void {
            $data = $validator->getData();
            $userId = $data[$field] ?? null;

            if (! $userId) {
                return;
            }

            $matches = OrganizationMembership::query()
                ->where('organization_id', $data['organization_id'] ?? 0)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where(function ($query) use ($data): void {
                    $query->whereNull('farm_id')->orWhere('farm_id', $data['farm_id'] ?? 0);
                })
                ->exists();

            if (! $matches) {
                $validator->errors()->add($field, 'The selected user must have active access to the selected organization and farm.');
            }
        });
    }

    protected function addProductOrganizationValidation(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $data = $validator->getData();
            if (! empty($data['product_id']) && ! InventoryProduct::query()->where('id', $data['product_id'])->where('organization_id', $data['organization_id'] ?? 0)->exists()) {
                $validator->errors()->add('product_id', 'The selected product must belong to the selected organization.');
            }
        });
    }

    protected function idBelongsToFarm(string $table, int $id, int $farmId): bool
    {
        return \DB::table($table)->where('id', $id)->where('farm_id', $farmId)->exists();
    }

    protected function nextNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('YmdHis').'-'.strtoupper(substr(uniqid(), -5));
    }
}
