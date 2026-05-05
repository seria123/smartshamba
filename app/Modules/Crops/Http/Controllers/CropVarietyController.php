<?php

namespace App\Modules\Crops\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropVariety;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CropVarietyController extends Controller
{
    public function index(): View { return view('crops::varieties.index', ['varieties' => CropVariety::with('crop')->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('crops::varieties.form', $this->formData()); }
    public function show(CropVariety $variety): View { return view('crops::varieties.show', ['variety' => $variety->load('crop')]); }
    public function edit(CropVariety $variety): View { return view('crops::varieties.form', $this->formData($variety)); }
    public function store(Request $request): RedirectResponse { CropVariety::query()->create($this->validated($request)); return redirect()->route('crops.varieties.index')->with('status', 'Variety created.'); }
    public function update(Request $request, CropVariety $variety): RedirectResponse { $variety->update($this->validated($request)); return redirect()->route('crops.varieties.show', $variety)->with('status', 'Variety updated.'); }
    public function deactivate(CropVariety $variety): RedirectResponse { $variety->update(['status' => 'inactive']); return redirect()->route('crops.varieties.show', $variety)->with('status', 'Variety deactivated.'); }

    private function formData(?CropVariety $variety = null): array { return ['variety' => $variety, 'crops' => Crop::query()->orderBy('name')->get()]; }

    private function validated(Request $request): array
    {
        return $request->validate([
            'crop_id' => ['required', 'integer', Rule::exists('crop_crops', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'expected_growing_days' => ['nullable', 'integer', 'min:1'],
            'seed_rate' => ['nullable', 'numeric', 'min:0'],
            'seed_rate_unit' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
