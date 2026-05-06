<?php

namespace App\Modules\Livestock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use App\Modules\Livestock\Models\LivestockBreed;
use App\Modules\Livestock\Models\LivestockSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LivestockBreedController extends Controller
{
    public function index(): View { return view('livestock::breeds.index', ['breeds' => LivestockBreed::with(['organization', 'species'])->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('livestock::breeds.form', $this->formData()); }
    public function show(LivestockBreed $breed): View { return view('livestock::breeds.show', ['breed' => $breed->load(['organization', 'species'])]); }
    public function edit(LivestockBreed $breed): View { return view('livestock::breeds.form', $this->formData($breed)); }
    public function store(Request $request): RedirectResponse { LivestockBreed::query()->create($this->validated($request) + ['created_by' => $request->user()->id]); return redirect()->route('livestock.breeds.index')->with('status', 'Breed created.'); }
    public function update(Request $request, LivestockBreed $breed): RedirectResponse { $breed->update($this->validated($request, $breed) + ['updated_by' => $request->user()->id]); return redirect()->route('livestock.breeds.show', $breed)->with('status', 'Breed updated.'); }
    public function deactivate(LivestockBreed $breed): RedirectResponse { $breed->update(['status' => 'inactive']); return redirect()->route('livestock.breeds.show', $breed)->with('status', 'Breed deactivated.'); }

    private function formData(?LivestockBreed $breed = null): array
    {
        return [
            'breed' => $breed,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'species' => LivestockSpecies::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?LivestockBreed $breed = null): array
    {
        return $request->validate([
            'organization_id' => ['nullable', 'integer', Rule::exists('organizations', 'id')],
            'species_id' => ['required', 'integer', Rule::exists('livestock_species', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('livestock_breeds', 'code')->where('species_id', $request->input('species_id'))->ignore($breed?->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])],
        ]);
    }
}
