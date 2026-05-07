<?php

namespace App\Modules\Finance\Services;

use App\Modules\Core\Models\Farm;
use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Finance\Models\FinanceCostCategory;
use App\Modules\Finance\Models\FinanceCostCentre;
use App\Modules\Finance\Models\FinanceCostEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CostEntryService
{
    public function create(array $data, ?int $userId = null): FinanceCostEntry
    {
        return DB::transaction(function () use ($data, $userId): FinanceCostEntry {
            $this->validateScope($data);
            $this->validateAmountMath($data);

            $allocation = $this->allocationData($data);
            $entry = FinanceCostEntry::query()->create($this->entryData($data) + [
                'entry_no' => $data['entry_no'] ?? $this->nextNumber((int) $data['organization_id']),
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            $entry->allocations()->create($allocation + [
                'organization_id' => $entry->organization_id,
                'farm_id' => $entry->farm_id,
            ]);

            $this->ensureAllocationTotalMatches($entry->fresh('allocations'));

            return $entry;
        });
    }

    public function updateDraft(FinanceCostEntry $entry, array $data): FinanceCostEntry
    {
        if ($entry->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft entries can be edited.']);
        }

        return DB::transaction(function () use ($entry, $data): FinanceCostEntry {
            $data['organization_id'] = $entry->organization_id;
            $this->validateScope($data);
            $this->validateAmountMath($data);

            $entry->update($this->entryData($data));
            $entry->allocations()->delete();
            $entry->allocations()->create($this->allocationData($data) + [
                'organization_id' => $entry->organization_id,
                'farm_id' => $entry->farm_id,
            ]);
            $this->ensureAllocationTotalMatches($entry->fresh('allocations'));

            return $entry;
        });
    }

    public function confirm(FinanceCostEntry $entry, int $userId): FinanceCostEntry
    {
        if ($entry->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft entries can be confirmed.']);
        }

        $this->ensureAllocationTotalMatches($entry->load('allocations'));
        $entry->update(['status' => 'confirmed', 'confirmed_by' => $userId, 'confirmed_at' => now()]);

        return $entry;
    }

    public function void(FinanceCostEntry $entry, string $reason, int $userId): FinanceCostEntry
    {
        if (! in_array($entry->status, ['draft', 'confirmed'], true)) {
            throw ValidationException::withMessages(['status' => 'Only draft or confirmed entries can be voided.']);
        }

        $entry->update(['status' => 'void', 'void_reason' => $reason, 'voided_by' => $userId, 'voided_at' => now()]);

        return $entry;
    }

    public function ensureAllocationTotalMatches(FinanceCostEntry $entry): void
    {
        $allocated = round((float) $entry->allocations->sum('amount'), 2);
        $amount = round((float) $entry->amount, 2);

        if (abs($allocated - $amount) > 0.01) {
            throw ValidationException::withMessages(['allocation_amount' => 'Allocation amounts must equal the cost entry amount.']);
        }
    }

    private function validateScope(array $data): void
    {
        if (! empty($data['farm_id']) && ! Farm::query()->where('id', $data['farm_id'])->where('organization_id', $data['organization_id'])->exists()) {
            throw ValidationException::withMessages(['farm_id' => 'The selected farm must belong to the selected organization.']);
        }

        $category = FinanceCostCategory::query()->findOrFail($data['cost_category_id']);
        if ($category->organization_id && (int) $category->organization_id !== (int) $data['organization_id']) {
            throw ValidationException::withMessages(['cost_category_id' => 'The selected category must belong to the selected organization.']);
        }
        if ($category->farm_id && (int) $category->farm_id !== (int) ($data['farm_id'] ?? 0)) {
            throw ValidationException::withMessages(['cost_category_id' => 'The selected category must belong to the selected farm.']);
        }

        if (! empty($data['cost_centre_id'])) {
            $centre = FinanceCostCentre::query()->findOrFail($data['cost_centre_id']);
            if ((int) $centre->organization_id !== (int) $data['organization_id']) {
                throw ValidationException::withMessages(['cost_centre_id' => 'The selected cost centre must belong to the selected organization.']);
            }
            if ($centre->farm_id && (int) $centre->farm_id !== (int) ($data['farm_id'] ?? 0)) {
                throw ValidationException::withMessages(['cost_centre_id' => 'The selected cost centre must belong to the selected farm.']);
            }
        }
    }

    private function validateAmountMath(array $data): void
    {
        if (! empty($data['quantity']) && ! empty($data['unit_cost'])) {
            $expected = round((float) $data['quantity'] * (float) $data['unit_cost'], 2);
            if (abs($expected - (float) $data['amount']) > 0.01) {
                throw ValidationException::withMessages(['amount' => 'Amount must equal quantity multiplied by unit cost.']);
            }
        }
    }

    private function entryData(array $data): array
    {
        return collect($data)->only(['organization_id', 'farm_id', 'cost_category_id', 'cost_centre_id', 'entry_date', 'title', 'description', 'source_module', 'reference_type', 'reference_id', 'reference_label', 'quantity', 'unit', 'unit_cost', 'amount', 'currency', 'payment_state', 'notes'])->all();
    }

    private function allocationData(array $data): array
    {
        return [
            'allocation_type' => $data['allocation_type'] ?? 'general_farm',
            'allocatable_type' => $data['allocatable_type'] ?? null,
            'allocatable_id' => $data['allocatable_id'] ?? null,
            'allocation_label' => $data['allocation_label'] ?? null,
            'allocation_percent' => $data['allocation_percent'] ?? 100,
            'amount' => $data['allocation_amount'] ?? $data['amount'],
            'notes' => $data['allocation_notes'] ?? null,
        ];
    }

    private function nextNumber(int $organizationId): string
    {
        return 'FIN-'.now()->format('YmdHis').'-'.$organizationId.'-'.strtoupper(substr(uniqid(), -4));
    }
}
