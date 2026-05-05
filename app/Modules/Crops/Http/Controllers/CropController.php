<?php

namespace App\Modules\Crops\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\Crop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CropController extends Controller
{
    public function index(): View { return view('crops::crops.index', ['crops' => Crop::with('organization')->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('crops::crops.form', $this->formData()); }
    public function show(Crop $crop): View { return view('crops::crops.show', ['crop' => $crop->load(['organization', 'varieties'])]); }
    public function edit(Crop $crop): View { return view('crops::crops.form', $this->formData($crop)); }
    public function store(Request $request): RedirectResponse { Crop::query()->create($this->validated($request)); return redirect()->route('crops.crops.index')->with('status', 'Crop created.'); }
    public function update(Request $request, Crop $crop): RedirectResponse { $crop->update($this->validated($request, $crop)); return redirect()->route('crops.crops.show', $crop)->with('status', 'Crop updated.'); }
    public function deactivate(Crop $crop): RedirectResponse { $crop->update(['status' => 'inactive']); return redirect()->route('crops.crops.show', $crop)->with('status', 'Crop deactivated.'); }

    private function formData(?Crop $crop = null): array
    {
        return ['crop' => $crop, 'organizations' => Organization::query()->orderBy('name')->get()];
    }

    private function validated(Request $request, ?Crop $crop = null): array
    {
        return $request->validate([
            'organization_id' => ['nullable', 'integer', Rule::exists('organizations', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('crop_crops', 'code')->where('organization_id', $request->input('organization_id'))->ignore($crop?->id)],
            'crop_type' => ['required', 'string', 'max:255'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'default_growing_days' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
