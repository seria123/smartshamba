<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Finance\Models\FinanceCostCentre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceCostCentreController extends Controller
{
    public function index(): View { return view('finance::cost-centres.index', ['centres' => FinanceCostCentre::with(['organization', 'farm'])->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('finance::cost-centres.form', $this->formData()); }
    public function show(FinanceCostCentre $costCentre): View { return view('finance::cost-centres.show', ['centre' => $costCentre->load(['organization', 'farm'])]); }
    public function edit(FinanceCostCentre $costCentre): View { return view('finance::cost-centres.form', $this->formData($costCentre)); }

    public function store(Request $request): RedirectResponse
    {
        FinanceCostCentre::query()->create($this->validated($request));
        return redirect()->route('finance.cost-centres.index')->with('status', 'Cost centre created.');
    }

    public function update(Request $request, FinanceCostCentre $costCentre): RedirectResponse
    {
        $costCentre->update($this->validated($request, $costCentre));
        return redirect()->route('finance.cost-centres.show', $costCentre)->with('status', 'Cost centre updated.');
    }

    public function deactivate(FinanceCostCentre $costCentre): RedirectResponse
    {
        $costCentre->update(['is_active' => false]);
        return redirect()->route('finance.cost-centres.show', $costCentre)->with('status', 'Cost centre deactivated.');
    }

    private function formData(?FinanceCostCentre $centre = null): array
    {
        return ['centre' => $centre, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get()];
    }

    private function validated(Request $request, ?FinanceCostCentre $centre = null): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['nullable', 'integer', 'exists:farms,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('finance_cost_centres')->where('organization_id', $request->input('organization_id'))->where('farm_id', $request->input('farm_id'))->ignore($centre?->id)],
            'code' => ['nullable', 'string', 'max:255'],
            'centre_type' => ['required', Rule::in(['farm', 'field', 'crop', 'livestock', 'irrigation', 'asset', 'labour', 'admin', 'project', 'other'])],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
