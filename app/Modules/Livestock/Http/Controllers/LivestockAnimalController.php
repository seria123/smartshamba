<?php

namespace App\Modules\Livestock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Livestock\Http\Controllers\Concerns\ValidatesLivestockScope;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockBreed;
use App\Modules\Livestock\Models\LivestockSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LivestockAnimalController extends Controller
{
    use ValidatesLivestockScope;

    public function index(): View
    {
        return view('livestock::animals.index', ['animals' => LivestockAnimal::with(['farm', 'paddock', 'species', 'breed'])->orderBy('animal_code')->paginate(20)]);
    }

    public function create(): View { return view('livestock::animals.form', $this->formData()); }

    public function show(LivestockAnimal $animal): View
    {
        return view('livestock::animals.show', [
            'animal' => $animal->load(['organization', 'farm', 'paddock', 'species', 'breed', 'treatments.withdrawalPeriods', 'breedingRecords', 'pregnancyChecks', 'birthRecordsAsMother', 'weightRecords', 'feedRecords.product', 'movementRecords.toPaddock', 'mortalityRecords', 'yieldRecords']),
        ] + $this->recordFormData());
    }

    public function edit(LivestockAnimal $animal): View { return view('livestock::animals.form', $this->formData($animal)); }

    public function store(Request $request): RedirectResponse
    {
        LivestockAnimal::query()->create($this->validated($request) + ['created_by' => $request->user()->id]);
        return redirect()->route('livestock.animals.index')->with('status', 'Animal registered.');
    }

    public function update(Request $request, LivestockAnimal $animal): RedirectResponse
    {
        $animal->update($this->validated($request, $animal) + ['updated_by' => $request->user()->id]);
        return redirect()->route('livestock.animals.show', $animal)->with('status', 'Animal updated.');
    }

    public function deactivate(LivestockAnimal $animal): RedirectResponse
    {
        $animal->update(['status' => 'inactive']);
        return redirect()->route('livestock.animals.show', $animal)->with('status', 'Animal deactivated.');
    }

    private function formData(?LivestockAnimal $animal = null): array
    {
        return $this->recordFormData() + [
            'animal' => $animal,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'animals' => LivestockAnimal::query()->orderBy('animal_code')->get(),
        ];
    }

    private function recordFormData(): array
    {
        return [
            'farms' => Farm::query()->orderBy('name')->get(),
            'paddocks' => Paddock::query()->orderBy('name')->get(),
            'species' => LivestockSpecies::query()->orderBy('name')->get(),
            'breeds' => LivestockBreed::query()->orderBy('name')->get(),
            'products' => InventoryProduct::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?LivestockAnimal $animal = null): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'paddock_id' => ['nullable', 'integer', Rule::exists('paddocks', 'id')],
            'species_id' => ['required', 'integer', Rule::exists('livestock_species', 'id')],
            'breed_id' => ['nullable', 'integer', Rule::exists('livestock_breeds', 'id')],
            'animal_code' => ['required', 'string', 'max:255', Rule::unique('livestock_animals', 'animal_code')->where('farm_id', $request->input('farm_id'))->ignore($animal?->id)],
            'tag_number' => ['nullable', 'string', 'max:255', Rule::unique('livestock_animals', 'tag_number')->where('farm_id', $request->input('farm_id'))->ignore($animal?->id)],
            'rfid_number' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', Rule::in(['male', 'female', 'unknown'])],
            'date_of_birth' => ['nullable', 'date'],
            'source' => ['required', Rule::in(['born_on_farm', 'purchased', 'transferred_in', 'donated', 'other'])],
            'status' => ['required', Rule::in(['active', 'sick', 'under_treatment', 'in_withdrawal', 'pregnant', 'lactating', 'sold', 'transferred_out', 'dead', 'culled', 'inactive', 'archived'])],
            'health_status' => ['nullable', Rule::in(['normal', 'watch', 'sick', 'critical', 'recovering', 'unknown'])],
            'production_status' => ['nullable', Rule::in(['growing', 'breeding', 'pregnant', 'lactating', 'dry', 'laying', 'finishing', 'retired', 'unknown'])],
            'dam_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')],
            'sire_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')],
            'current_weight' => ['nullable', 'numeric', 'min:0'],
            'weight_unit' => ['nullable', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->addAnimalScopeValidation($validator);

        return $validator->validate();
    }
}
