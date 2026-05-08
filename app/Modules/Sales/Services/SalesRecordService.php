<?php

namespace App\Modules\Sales\Services;

use App\Modules\Core\Models\Farm;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use App\Modules\Sales\Models\SalesCatalogItem;
use App\Modules\Sales\Models\SalesCustomer;
use App\Modules\Sales\Models\SalesRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesRecordService
{
    public function __construct(private SalesNumberService $numbers) {}

    public function create(array $data, ?int $userId = null): SalesRecord
    {
        return DB::transaction(function () use ($data, $userId): SalesRecord {
            $this->validateScope($data);
            $lines = $this->prepareLines($data);

            $record = SalesRecord::query()->create($this->recordData($data) + [
                'sale_number' => $this->numbers->next((int) $data['organization_id']),
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'created_by' => $userId,
            ]);

            $record->lines()->createMany($lines);
            $this->recalculate($record);

            return $record->fresh(['lines', 'customer']);
        });
    }

    public function updateDraft(SalesRecord $record, array $data, ?int $userId = null): SalesRecord
    {
        if ($record->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft sales can be edited.']);
        }

        return DB::transaction(function () use ($record, $data, $userId): SalesRecord {
            $data['organization_id'] = $record->organization_id;
            $this->validateScope($data);
            $lines = $this->prepareLines($data);

            $record->update($this->recordData($data) + ['updated_by' => $userId]);
            $record->lines()->delete();
            $record->lines()->createMany($lines);
            $this->recalculate($record);

            return $record->fresh(['lines', 'customer']);
        });
    }

    public function confirm(SalesRecord $record, ?int $userId): SalesRecord
    {
        if ($record->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft sales can be confirmed.']);
        }
        if ($record->lines()->count() === 0) {
            throw ValidationException::withMessages(['lines' => 'A sale must have at least one line before confirmation.']);
        }

        $this->recalculate($record);
        $record->update(['status' => 'confirmed', 'confirmed_by' => $userId, 'confirmed_at' => now()]);

        return $record;
    }

    public function void(SalesRecord $record, string $reason, ?int $userId): SalesRecord
    {
        if (! in_array($record->status, ['draft', 'confirmed'], true)) {
            throw ValidationException::withMessages(['status' => 'Only draft or confirmed sales can be voided.']);
        }

        $record->update(['status' => 'void', 'void_reason' => $reason, 'voided_by' => $userId, 'voided_at' => now()]);

        return $record;
    }

    public function recalculate(SalesRecord $record): SalesRecord
    {
        $record->load('lines', 'payments');
        $subtotal = round((float) $record->lines->sum('line_total_amount'), 2);
        $total = max(0, round($subtotal - (float) $record->discount_amount + (float) $record->other_charges_amount, 2));
        $paid = round((float) $record->payments->sum('amount'), 2);
        $balance = max(0, round($total - $paid, 2));

        $record->update([
            'subtotal_amount' => $subtotal,
            'total_amount' => $total,
            'amount_paid' => $paid,
            'balance_amount' => $balance,
            'payment_status' => $paid <= 0 ? 'unpaid' : ($balance <= 0.01 ? 'paid' : 'partial'),
        ]);

        return $record;
    }

    private function recordData(array $data): array
    {
        return collect($data)->only(['organization_id', 'farm_id', 'sales_customer_id', 'sale_date', 'channel', 'currency', 'discount_amount', 'other_charges_amount', 'notes'])->all() + [
            'currency' => $data['currency'] ?? 'KES',
            'discount_amount' => $data['discount_amount'] ?? 0,
            'other_charges_amount' => $data['other_charges_amount'] ?? 0,
        ];
    }

    private function prepareLines(array $data): array
    {
        $rawLines = collect($data['lines'] ?? [])->filter(fn ($line) => ! empty($line['description']) || ! empty($line['sales_catalog_item_id']))->values();
        if ($rawLines->isEmpty()) {
            throw ValidationException::withMessages(['lines' => 'A sale must have at least one line.']);
        }

        return $rawLines->map(function (array $line, int $index) use ($data): array {
            $this->validateLineScope($line, $data, $index);
            $quantity = (float) ($line['quantity'] ?? 0);
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $discount = (float) ($line['discount_amount'] ?? 0);
            $lineTotal = round(($quantity * $unitPrice) - $discount, 2);
            if ($quantity <= 0 || $unitPrice < 0 || $discount < 0 || $lineTotal < 0) {
                throw ValidationException::withMessages(['lines.'.$index => 'Line quantity, price, and discount produce an invalid total.']);
            }

            return collect($line)->only(['sales_catalog_item_id', 'description', 'category', 'unit', 'quantity', 'unit_price', 'discount_amount', 'crop_cycle_id', 'crop_harvest_id', 'animal_id', 'animal_group_id', 'livestock_yield_id', 'notes'])->all() + [
                'line_total_amount' => $lineTotal,
                'discount_amount' => $discount,
            ];
        })->all();
    }

    private function validateScope(array $data): void
    {
        if (! Farm::query()->where('id', $data['farm_id'])->where('organization_id', $data['organization_id'])->exists()) {
            throw ValidationException::withMessages(['farm_id' => 'The selected farm must belong to the selected organization.']);
        }

        if (! empty($data['sales_customer_id'])) {
            $customer = SalesCustomer::query()->findOrFail($data['sales_customer_id']);
            if ((int) $customer->organization_id !== (int) $data['organization_id'] || ($customer->farm_id && (int) $customer->farm_id !== (int) $data['farm_id'])) {
                throw ValidationException::withMessages(['sales_customer_id' => 'The selected customer must belong to the sale organization and farm scope.']);
            }
        }
    }

    private function validateLineScope(array $line, array $data, int $index): void
    {
        $selectedLinks = collect(['crop_cycle_id', 'crop_harvest_id', 'animal_id', 'animal_group_id', 'livestock_yield_id'])->filter(fn ($key) => ! empty($line[$key]))->count();
        if ($selectedLinks > 1) {
            throw ValidationException::withMessages(['lines.'.$index => 'Select only one operational source per sale line.']);
        }

        if (! empty($line['sales_catalog_item_id'])) {
            $item = SalesCatalogItem::query()->findOrFail($line['sales_catalog_item_id']);
            if ((int) $item->organization_id !== (int) $data['organization_id'] || ($item->farm_id && (int) $item->farm_id !== (int) $data['farm_id'])) {
                throw ValidationException::withMessages(['lines.'.$index.'.sales_catalog_item_id' => 'The selected catalog item must belong to the sale scope.']);
            }
        }

        $checks = [
            'crop_cycle_id' => CropCycle::class,
            'crop_harvest_id' => CropHarvestRecord::class,
            'animal_id' => LivestockAnimal::class,
            'animal_group_id' => LivestockAnimalGroup::class,
            'livestock_yield_id' => LivestockYieldRecord::class,
        ];

        foreach ($checks as $field => $model) {
            if (! empty($line[$field]) && ! $model::query()->where('id', $line[$field])->where('organization_id', $data['organization_id'])->where('farm_id', $data['farm_id'])->exists()) {
                throw ValidationException::withMessages(['lines.'.$index.'.'.$field => 'The selected linked record must belong to the sale organization and farm.']);
            }
        }
    }
}
