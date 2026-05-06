<?php

namespace App\Modules\Livestock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Livestock\Http\Controllers\Concerns\ValidatesLivestockScope;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockBirthRecord;
use App\Modules\Livestock\Models\LivestockBreedingRecord;
use App\Modules\Livestock\Models\LivestockEvent;
use App\Modules\Livestock\Models\LivestockFeedRecord;
use App\Modules\Livestock\Models\LivestockMortalityRecord;
use App\Modules\Livestock\Models\LivestockMovementRecord;
use App\Modules\Livestock\Models\LivestockPregnancyCheck;
use App\Modules\Livestock\Models\LivestockTreatmentRecord;
use App\Modules\Livestock\Models\LivestockWeightRecord;
use App\Modules\Livestock\Models\LivestockWithdrawalPeriod;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LivestockRecordController extends Controller
{
    use ValidatesLivestockScope;

    public function storeAnimalTreatment(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeTreatment($request, $animal); }
    public function storeGroupTreatment(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeTreatment($request, $group); }
    public function storeAnimalBreeding(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeBreeding($request, $animal); }
    public function storeGroupBreeding(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeBreeding($request, $group); }
    public function storeAnimalPregnancyCheck(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storePregnancyCheck($request, $animal); }
    public function storeGroupPregnancyCheck(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storePregnancyCheck($request, $group); }
    public function storeAnimalBirth(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeBirth($request, $animal); }
    public function storeGroupBirth(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeBirth($request, $group); }
    public function storeAnimalWeight(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeWeight($request, $animal); }
    public function storeGroupWeight(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeWeight($request, $group); }
    public function storeAnimalFeed(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeFeed($request, $animal); }
    public function storeGroupFeed(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeFeed($request, $group); }
    public function storeAnimalMovement(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeMovement($request, $animal); }
    public function storeGroupMovement(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeMovement($request, $group); }
    public function storeAnimalMortality(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeMortality($request, $animal); }
    public function storeGroupMortality(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeMortality($request, $group); }
    public function storeAnimalYield(Request $request, LivestockAnimal $animal): RedirectResponse { return $this->storeYield($request, $animal); }
    public function storeGroupYield(Request $request, LivestockAnimalGroup $group): RedirectResponse { return $this->storeYield($request, $group); }

    private function storeTreatment(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $validator = validator($request->all(), [
            'treatment_type' => ['required', Rule::in(['treatment', 'vaccination', 'deworming', 'preventive', 'first_aid', 'surgery', 'other'])],
            'treatment_date' => ['required', 'date'],
            'condition_or_reason' => ['nullable', 'string', 'max:255'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer', Rule::exists('inventory_products', 'id')],
            'product_name_snapshot' => ['nullable', 'string', 'max:255'],
            'dosage' => ['nullable', 'numeric', 'min:0'],
            'dosage_unit' => ['nullable', 'string', 'max:50'],
            'route' => ['nullable', Rule::in(['oral', 'injection', 'intramuscular', 'subcutaneous', 'topical', 'spray', 'water', 'feed', 'other'])],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'withdrawal_meat_days' => ['nullable', 'integer', 'min:0'],
            'withdrawal_milk_days' => ['nullable', 'integer', 'min:0'],
            'withdrawal_egg_days' => ['nullable', 'integer', 'min:0'],
            'follow_up_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addProductOrganizationValidation($validator, $target->organization_id);
        $data = $validator->validate();

        $product = ! empty($data['product_id']) ? InventoryProduct::query()->find($data['product_id']) : null;
        $treatment = LivestockTreatmentRecord::query()->create($this->targetData($target) + $data + [
            'product_name_snapshot' => $data['product_name_snapshot'] ?? $product?->name,
            'created_by' => $request->user()->id,
        ]);
        $this->recordEvent($target, 'treatment', $data['treatment_date'], $request->user()->id, $data['notes'] ?? null);
        $this->createWithdrawals($treatment, $request->user()->id);

        return $this->backToTarget($target, 'Treatment recorded.');
    }

    private function storeBreeding(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $data = $request->validate([
            'breeding_date' => ['required', 'date'],
            'breeding_method' => ['required', Rule::in(['natural', 'artificial_insemination', 'controlled_mating', 'unknown', 'other'])],
            'male_animal_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')],
            'sire_name_snapshot' => ['nullable', 'string', 'max:255'],
            'expected_due_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['recorded', 'confirmed_pregnant', 'failed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);

        LivestockBreedingRecord::query()->create($this->targetData($target) + $data + ['status' => $data['status'] ?? 'recorded', 'created_by' => $request->user()->id]);
        $this->recordEvent($target, 'breeding', $data['breeding_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Breeding recorded.');
    }

    private function storePregnancyCheck(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $data = $request->validate([
            'related_breeding_record_id' => ['nullable', 'integer', Rule::exists('livestock_breeding_records', 'id')],
            'check_date' => ['required', 'date'],
            'result' => ['required', Rule::in(['pregnant', 'not_pregnant', 'uncertain', 'recheck_needed'])],
            'expected_due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        LivestockPregnancyCheck::query()->create($this->targetData($target) + $data + ['created_by' => $request->user()->id]);
        if ($target instanceof LivestockAnimal && $data['result'] === 'pregnant') {
            $target->update(['status' => 'pregnant', 'production_status' => 'pregnant']);
        }
        $this->recordEvent($target, 'pregnancy_check', $data['check_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Pregnancy check recorded.');
    }

    private function storeBirth(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $data = $request->validate([
            'birth_date' => ['required', 'date'],
            'number_born' => ['required', 'integer', 'min:0'],
            'number_alive' => ['required', 'integer', 'min:0'],
            'number_dead' => ['required', 'integer', 'min:0'],
            'birth_type' => ['nullable', Rule::in(['normal', 'assisted', 'complicated', 'unknown'])],
            'notes' => ['nullable', 'string'],
        ]);

        LivestockBirthRecord::query()->create($this->targetData($target, motherKey: true) + $data + ['recorded_by' => $request->user()->id]);
        $this->recordEvent($target, 'birth', $data['birth_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Birth recorded.');
    }

    private function storeWeight(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $data = $request->validate([
            'weigh_date' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'gt:0'],
            'weight_unit' => ['required', 'string', 'max:50'],
            'measurement_method' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        LivestockWeightRecord::query()->create($this->targetData($target) + $data + ['recorded_by' => $request->user()->id]);
        if ($target instanceof LivestockAnimal) {
            $target->update(['current_weight' => $data['weight'], 'weight_unit' => $data['weight_unit']]);
        }
        $this->recordEvent($target, 'weight', $data['weigh_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Weight recorded.');
    }

    private function storeFeed(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $validator = validator($request->all(), [
            'feed_date' => ['required', 'date'],
            'product_id' => ['nullable', 'integer', Rule::exists('inventory_products', 'id')],
            'feed_name_snapshot' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'quantity_unit' => ['required', 'string', 'max:50'],
            'feeding_method' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addProductOrganizationValidation($validator, $target->organization_id);
        $data = $validator->validate();
        $product = ! empty($data['product_id']) ? InventoryProduct::query()->find($data['product_id']) : null;

        LivestockFeedRecord::query()->create($this->targetData($target) + $data + [
            'feed_name_snapshot' => $data['feed_name_snapshot'] ?? $product?->name,
            'created_by' => $request->user()->id,
        ]);
        $this->recordEvent($target, 'feed', $data['feed_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Feed recorded.');
    }

    private function storeMovement(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $validator = validator($request->all(), [
            'from_paddock_id' => ['nullable', 'integer', Rule::exists('paddocks', 'id')],
            'to_paddock_id' => ['nullable', 'integer', Rule::exists('paddocks', 'id')],
            'movement_date' => ['required', 'date'],
            'movement_reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addMovementPaddockValidation($validator, $target->farm_id);
        $data = $validator->validate();

        LivestockMovementRecord::query()->create($this->targetData($target) + $data + ['created_by' => $request->user()->id]);
        if (! empty($data['to_paddock_id'])) {
            $target->update(['paddock_id' => $data['to_paddock_id']]);
        }
        $this->recordEvent($target, 'movement', $data['movement_date'], $request->user()->id, $data['notes'] ?? null, $data['to_paddock_id'] ?? null);

        return $this->backToTarget($target, 'Movement recorded.');
    }

    private function storeMortality(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $validator = validator($request->all(), [
            'mortality_date' => ['required', 'date'],
            'number_dead' => [$target instanceof LivestockAnimalGroup ? 'required' : 'nullable', 'integer', 'min:1'],
            'cause' => ['nullable', 'string', 'max:255'],
            'suspected_reason' => ['nullable', 'string', 'max:255'],
            'disposal_method' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
        if ($target instanceof LivestockAnimalGroup) {
            $this->addGroupMortalityValidation($validator, $target);
        }
        $data = $validator->validate();

        LivestockMortalityRecord::query()->create($this->targetData($target) + $data + ['reported_by' => $request->user()->id]);
        if ($target instanceof LivestockAnimal) {
            $target->update(['status' => 'dead']);
        } else {
            $target->update(['current_count' => max(0, $target->current_count - (int) $data['number_dead'])]);
        }
        $this->recordEvent($target, 'mortality', $data['mortality_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Mortality recorded.');
    }

    private function storeYield(Request $request, LivestockAnimal|LivestockAnimalGroup $target): RedirectResponse
    {
        $data = $request->validate([
            'yield_date' => ['required', 'date'],
            'yield_type' => ['required', Rule::in(['milk', 'eggs', 'weight_gain', 'honey', 'fish', 'wool', 'manure', 'other'])],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'grade' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        LivestockYieldRecord::query()->create($this->targetData($target) + $data + ['recorded_by' => $request->user()->id]);
        $this->recordEvent($target, 'yield', $data['yield_date'], $request->user()->id, $data['notes'] ?? null);

        return $this->backToTarget($target, 'Yield recorded.');
    }

    private function createWithdrawals(LivestockTreatmentRecord $treatment, int $userId): void
    {
        foreach (['meat' => $treatment->withdrawal_meat_days, 'milk' => $treatment->withdrawal_milk_days, 'eggs' => $treatment->withdrawal_egg_days] as $type => $days) {
            if ($days === null || (int) $days <= 0) {
                continue;
            }

            $startsOn = CarbonImmutable::parse($treatment->treatment_date);
            LivestockWithdrawalPeriod::query()->create([
                'organization_id' => $treatment->organization_id,
                'farm_id' => $treatment->farm_id,
                'animal_id' => $treatment->animal_id,
                'animal_group_id' => $treatment->animal_group_id,
                'treatment_record_id' => $treatment->id,
                'withdrawal_type' => $type,
                'starts_on' => $startsOn->toDateString(),
                'ends_on' => $startsOn->addDays((int) $days)->toDateString(),
                'status' => 'active',
                'created_by' => $userId,
            ]);
        }
    }

    private function recordEvent(LivestockAnimal|LivestockAnimalGroup $target, string $type, string $date, int $userId, ?string $notes, ?int $paddockId = null): void
    {
        LivestockEvent::query()->create($this->targetData($target) + [
            'paddock_id' => $paddockId ?? $target->paddock_id,
            'event_number' => 'LVE-'.now()->format('YmdHis').'-'.strtoupper(substr(uniqid(), -5)),
            'event_type' => $type,
            'event_date' => $date,
            'status' => 'recorded',
            'performed_by_user_id' => $userId,
            'notes' => $notes,
            'created_by' => $userId,
        ]);
    }

    private function targetData(LivestockAnimal|LivestockAnimalGroup $target, bool $motherKey = false): array
    {
        $data = [
            'organization_id' => $target->organization_id,
            'farm_id' => $target->farm_id,
        ];

        if ($target instanceof LivestockAnimal) {
            $data[$motherKey ? 'mother_animal_id' : 'animal_id'] = $target->id;
        } else {
            $data['animal_group_id'] = $target->id;
        }

        return $data;
    }

    private function backToTarget(LivestockAnimal|LivestockAnimalGroup $target, string $message): RedirectResponse
    {
        $route = $target instanceof LivestockAnimal ? 'livestock.animals.show' : 'livestock.groups.show';

        return redirect()->route($route, $target)->with('status', $message);
    }
}
