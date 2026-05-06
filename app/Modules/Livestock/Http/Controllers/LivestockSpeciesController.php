<?php

namespace App\Modules\Livestock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use App\Modules\Livestock\Models\LivestockSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LivestockSpeciesController extends Controller
{
    public function index(): View { return view('livestock::species.index', ['species' => LivestockSpecies::with('organization')->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('livestock::species.form', $this->formData()); }
    public function show(LivestockSpecies $species): View { return view('livestock::species.show', ['species' => $species->load(['organization', 'breeds'])]); }
    public function edit(LivestockSpecies $species): View { return view('livestock::species.form', $this->formData($species)); }
    public function store(Request $request): RedirectResponse { LivestockSpecies::query()->create($this->validated($request) + ['created_by' => $request->user()->id]); return redirect()->route('livestock.species.index')->with('status', 'Species created.'); }
    public function update(Request $request, LivestockSpecies $species): RedirectResponse { $species->update($this->validated($request, $species) + ['updated_by' => $request->user()->id]); return redirect()->route('livestock.species.show', $species)->with('status', 'Species updated.'); }
    public function deactivate(LivestockSpecies $species): RedirectResponse { $species->update(['status' => 'inactive']); return redirect()->route('livestock.species.show', $species)->with('status', 'Species deactivated.'); }

    private function formData(?LivestockSpecies $species = null): array
    {
        return ['species' => $species, 'organizations' => Organization::query()->orderBy('name')->get()];
    }

    private function validated(Request $request, ?LivestockSpecies $species = null): array
    {
        return $request->validate([
            'organization_id' => ['nullable', 'integer', Rule::exists('organizations', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('livestock_species', 'code')->where('organization_id', $request->input('organization_id'))->ignore($species?->id)],
            'species_type' => ['required', Rule::in(['mammal', 'bird', 'fish', 'insect', 'other'])],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])],
        ]);
    }
}
