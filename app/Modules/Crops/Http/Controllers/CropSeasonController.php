<?php

namespace App\Modules\Crops\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Http\Controllers\Concerns\ValidatesCropScope;
use App\Modules\Crops\Models\CropSeason;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CropSeasonController extends Controller
{
    use ValidatesCropScope;

    public function index(): View { return view('crops::seasons.index', ['seasons' => CropSeason::with(['organization', 'farm'])->latest()->paginate(20)]); }
    public function create(): View { return view('crops::seasons.form', $this->formData()); }
    public function show(CropSeason $season): View { return view('crops::seasons.show', ['season' => $season->load(['organization', 'farm', 'cycles'])]); }
    public function edit(CropSeason $season): View { return view('crops::seasons.form', $this->formData($season)); }
    public function store(Request $request): RedirectResponse { CropSeason::query()->create($this->validated($request)); return redirect()->route('crops.seasons.index')->with('status', 'Season created.'); }
    public function update(Request $request, CropSeason $season): RedirectResponse { $season->update($this->validated($request)); return redirect()->route('crops.seasons.show', $season)->with('status', 'Season updated.'); }
    public function close(CropSeason $season): RedirectResponse { $season->update(['status' => 'closed']); return redirect()->route('crops.seasons.show', $season)->with('status', 'Season closed.'); }

    private function formData(?CropSeason $season = null): array
    {
        return ['season' => $season, 'organizations' => Organization::query()->orderBy('name')->get(), 'farms' => Farm::query()->with('organization')->orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['nullable', 'integer', Rule::exists('farms', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'season_type' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['planned', 'active', 'closed', 'cancelled'])],
        ]);
        if ($data['farm_id'] ?? null) {
            $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        }

        return $data;
    }
}
