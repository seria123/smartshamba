<?php

namespace App\Modules\Livestock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Livestock\Http\Controllers\Concerns\ValidatesLivestockScope;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockBreed;
use App\Modules\Livestock\Models\LivestockSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LivestockAnimalGroupController extends Controller
{
    use ValidatesLivestockScope;

    public function index(): View
    {
        return view('livestock::groups.index', ['groups' => LivestockAnimalGroup::with(['farm', 'paddock', 'species', 'breed'])->orderBy('group_code')->paginate(20)]);
    }

    public function create(): View { return view('livestock::groups.form', $this->formData()); }

    public function show(LivestockAnimalGroup $group): View
    {
        return view('livestock::groups.show', [
            'group' => $group->load(['organization', 'farm', 'paddock', 'species', 'breed', 'treatments.withdrawalPeriods', 'breedingRecords', 'pregnancyChecks', 'birthRecords', 'weightRecords', 'feedRecords.product', 'movementRecords.toPaddock', 'mortalityRecords', 'yieldRecords']),
        ] + $this->recordFormData());
    }

    public function edit(LivestockAnimalGroup $group): View { return view('livestock::groups.form', $this->formData($group)); }

    public function store(Request $request): RedirectResponse
    {
        LivestockAnimalGroup::query()->create($this->validated($request) + ['created_by' => $request->user()->id]);
        return redirect()->route('livestock.groups.index')->with('status', 'Animal group registered.');
    }

    public function update(Request $request, LivestockAnimalGroup $group): RedirectResponse
    {
        $group->update($this->validated($request, $group) + ['updated_by' => $request->user()->id]);
        return redirect()->route('livestock.groups.show', $group)->with('status', 'Animal group updated.');
    }

    public function deactivate(LivestockAnimalGroup $group): RedirectResponse
    {
        $group->update(['status' => 'inactive']);
        return redirect()->route('livestock.groups.show', $group)->with('status', 'Animal group deactivated.');
    }

    private function formData(?LivestockAnimalGroup $group = null): array
    {
        return $this->recordFormData() + ['group' => $group, 'organizations' => Organization::query()->orderBy('name')->get()];
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

    private function validated(Request $request, ?LivestockAnimalGroup $group = null): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'paddock_id' => ['nullable', 'integer', Rule::exists('paddocks', 'id')],
            'species_id' => ['required', 'integer', Rule::exists('livestock_species', 'id')],
            'breed_id' => ['nullable', 'integer', Rule::exists('livestock_breeds', 'id')],
            'group_code' => ['required', 'string', 'max:255', Rule::unique('livestock_animal_groups', 'group_code')->where('farm_id', $request->input('farm_id'))->ignore($group?->id)],
            'name' => ['required', 'string', 'max:255'],
            'group_type' => ['required', Rule::in(['flock', 'herd', 'batch', 'pond', 'pen_group', 'age_group', 'other'])],
            'start_date' => ['nullable', 'date'],
            'initial_count' => ['required', 'integer', 'min:0'],
            'current_count' => ['required', 'integer', 'min:0'],
            'sex_composition' => ['nullable', 'string', 'max:255'],
            'source' => ['required', Rule::in(['born_on_farm', 'purchased', 'transferred_in', 'donated', 'other'])],
            'status' => ['required', Rule::in(['active', 'under_treatment', 'in_withdrawal', 'sold', 'transferred_out', 'closed', 'inactive', 'archived'])],
            'health_status' => ['nullable', Rule::in(['normal', 'watch', 'sick', 'critical', 'recovering', 'unknown'])],
            'production_status' => ['nullable', Rule::in(['growing', 'breeding', 'pregnant', 'lactating', 'dry', 'laying', 'finishing', 'retired', 'unknown'])],
            'notes' => ['nullable', 'string'],
        ]);

        $this->addGroupScopeValidation($validator);

        return $validator->validate();
    }
}
