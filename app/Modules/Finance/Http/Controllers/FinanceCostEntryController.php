<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Finance\Models\FinanceCostCategory;
use App\Modules\Finance\Models\FinanceCostCentre;
use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Finance\Services\CostEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceCostEntryController extends Controller
{
    public function index(Request $request): View
    {
        $entries = FinanceCostEntry::with(['category', 'centre', 'farm', 'allocations'])
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('source_module'), fn ($query, $source) => $query->where('source_module', $source))
            ->when($request->input('cost_category_id'), fn ($query, $id) => $query->where('cost_category_id', $id))
            ->when($request->input('cost_centre_id'), fn ($query, $id) => $query->where('cost_centre_id', $id))
            ->when($request->input('farm_id'), fn ($query, $id) => $query->where('farm_id', $id))
            ->when($request->input('allocation_type'), fn ($query, $type) => $query->whereHas('allocations', fn ($allocations) => $allocations->where('allocation_type', $type)))
            ->latest('entry_date')
            ->paginate(20);

        return view('finance::cost-entries.index', ['entries' => $entries] + $this->formData());
    }

    public function create(): View { return view('finance::cost-entries.form', $this->formData()); }
    public function show(FinanceCostEntry $costEntry): View { return view('finance::cost-entries.show', ['entry' => $costEntry->load(['organization', 'farm', 'category', 'centre', 'allocations', 'createdBy', 'confirmedBy', 'voidedBy'])]); }
    public function edit(FinanceCostEntry $costEntry): View { abort_unless($costEntry->status === 'draft', 403); return view('finance::cost-entries.form', $this->formData($costEntry)); }

    public function store(Request $request, CostEntryService $service): RedirectResponse
    {
        $entry = $service->create($this->validated($request), $request->user()?->id);
        return redirect()->route('finance.cost-entries.show', $entry)->with('status', 'Cost entry created.');
    }

    public function update(Request $request, FinanceCostEntry $costEntry, CostEntryService $service): RedirectResponse
    {
        $service->updateDraft($costEntry, $this->validated($request));
        return redirect()->route('finance.cost-entries.show', $costEntry)->with('status', 'Cost entry updated.');
    }

    public function confirm(Request $request, FinanceCostEntry $costEntry, CostEntryService $service): RedirectResponse
    {
        $service->confirm($costEntry, $request->user()->id);
        return redirect()->route('finance.cost-entries.show', $costEntry)->with('status', 'Cost entry confirmed.');
    }

    public function void(Request $request, FinanceCostEntry $costEntry, CostEntryService $service): RedirectResponse
    {
        $data = $request->validate(['void_reason' => ['required', 'string', 'max:2000']]);
        $service->void($costEntry, $data['void_reason'], $request->user()->id);
        return redirect()->route('finance.cost-entries.show', $costEntry)->with('status', 'Cost entry voided.');
    }

    public function destroy(FinanceCostEntry $costEntry): RedirectResponse
    {
        abort_unless($costEntry->status === 'draft', 403);
        $costEntry->delete();
        return redirect()->route('finance.cost-entries.index')->with('status', 'Draft cost entry deleted.');
    }

    private function formData(?FinanceCostEntry $entry = null): array
    {
        return [
            'entry' => $entry,
            'organizations' => Organization::orderBy('name')->get(),
            'farms' => Farm::orderBy('name')->get(),
            'categories' => FinanceCostCategory::where('is_active', true)->orderBy('name')->get(),
            'centres' => FinanceCostCentre::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['nullable', 'integer', 'exists:farms,id'],
            'cost_category_id' => ['required', 'integer', 'exists:finance_cost_categories,id'],
            'cost_centre_id' => ['nullable', 'integer', 'exists:finance_cost_centres,id'],
            'entry_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source_module' => ['required', Rule::in(['manual', 'labour', 'task', 'inventory', 'crop', 'livestock', 'irrigation', 'asset', 'maintenance', 'other'])],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'integer', 'min:1'],
            'reference_label' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'numeric', 'gt:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_cost' => ['nullable', 'numeric', 'gte:0'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'payment_state' => ['nullable', Rule::in(['not_tracked', 'unpaid', 'part_paid', 'paid'])],
            'notes' => ['nullable', 'string'],
            'allocation_type' => ['required', Rule::in(['general_farm', 'field', 'crop_cycle', 'crop_activity', 'livestock_animal', 'livestock_group', 'livestock_event', 'irrigation_event', 'asset', 'maintenance_record', 'work_order', 'task', 'inventory_movement', 'worker', 'team', 'other'])],
            'allocatable_type' => ['nullable', 'string', 'max:255'],
            'allocatable_id' => ['nullable', 'integer', 'min:1'],
            'allocation_label' => ['nullable', 'string', 'max:255'],
            'allocation_percent' => ['nullable', 'numeric', 'gt:0', 'lte:100'],
            'allocation_amount' => ['nullable', 'numeric', 'gt:0'],
            'allocation_notes' => ['nullable', 'string'],
        ]) + ['currency' => $request->input('currency', 'KES')];
    }
}
