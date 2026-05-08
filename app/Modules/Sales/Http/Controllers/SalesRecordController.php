<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use App\Modules\Sales\Models\SalesCatalogItem;
use App\Modules\Sales\Models\SalesCustomer;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\Sales\Services\SalesRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesRecordController extends Controller
{
    public function index(Request $request): View
    {
        $records = SalesRecord::with(['customer', 'farm'])
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('payment_status'), fn ($query, $status) => $query->where('payment_status', $status))
            ->when($request->input('farm_id'), fn ($query, $id) => $query->where('farm_id', $id))
            ->when($request->input('sales_customer_id'), fn ($query, $id) => $query->where('sales_customer_id', $id))
            ->latest('sale_date')->paginate(20);

        return view('sales::records.index', ['records' => $records] + $this->formData());
    }

    public function create(): View { return view('sales::records.form', $this->formData()); }
    public function show(SalesRecord $record): View { return view('sales::records.show', ['record' => $record->load(['organization', 'farm', 'customer', 'lines.catalogItem', 'payments.createdBy', 'createdBy', 'confirmedBy', 'voidedBy'])]); }
    public function edit(SalesRecord $record): View { abort_unless($record->status === 'draft', 403); return view('sales::records.form', $this->formData($record->load('lines'))); }

    public function store(Request $request, SalesRecordService $service): RedirectResponse
    {
        $record = $service->create($this->validated($request), $request->user()?->id);
        return redirect()->route('sales.records.show', $record)->with('status', 'Sale record created.');
    }

    public function update(Request $request, SalesRecord $record, SalesRecordService $service): RedirectResponse
    {
        $service->updateDraft($record, $this->validated($request), $request->user()?->id);
        return redirect()->route('sales.records.show', $record)->with('status', 'Sale record updated.');
    }

    public function confirm(Request $request, SalesRecord $record, SalesRecordService $service): RedirectResponse
    {
        $service->confirm($record, $request->user()->id);
        return redirect()->route('sales.records.show', $record)->with('status', 'Sale confirmed.');
    }

    public function void(Request $request, SalesRecord $record, SalesRecordService $service): RedirectResponse
    {
        $data = $request->validate(['void_reason' => ['required', 'string', 'max:2000']]);
        $service->void($record, $data['void_reason'], $request->user()->id);
        return redirect()->route('sales.records.show', $record)->with('status', 'Sale voided.');
    }

    public function destroy(SalesRecord $record): RedirectResponse
    {
        abort_unless($record->status === 'draft', 403);
        $record->delete();
        return redirect()->route('sales.records.index')->with('status', 'Draft sale deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'sales_customer_id' => ['nullable', 'integer', 'exists:sales_customers,id'],
            'sale_date' => ['required', 'date'],
            'channel' => ['nullable', Rule::in(['farm_gate', 'market', 'contract', 'delivery', 'cooperative', 'broker', 'other'])],
            'currency' => ['nullable', 'string', 'size:3'],
            'discount_amount' => ['nullable', 'numeric', 'gte:0'],
            'other_charges_amount' => ['nullable', 'numeric', 'gte:0'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.sales_catalog_item_id' => ['nullable', 'integer', 'exists:sales_catalog_items,id'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.category' => ['nullable', Rule::in(['crop_produce', 'livestock', 'animal_product', 'service', 'other'])],
            'lines.*.unit' => ['required', 'string', 'max:50'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['required', 'numeric', 'gte:0'],
            'lines.*.discount_amount' => ['nullable', 'numeric', 'gte:0'],
            'lines.*.crop_cycle_id' => ['nullable', 'integer', 'exists:crop_cycles,id'],
            'lines.*.crop_harvest_id' => ['nullable', 'integer', 'exists:crop_harvest_records,id'],
            'lines.*.animal_id' => ['nullable', 'integer', 'exists:livestock_animals,id'],
            'lines.*.animal_group_id' => ['nullable', 'integer', 'exists:livestock_animal_groups,id'],
            'lines.*.livestock_yield_id' => ['nullable', 'integer', 'exists:livestock_yield_records,id'],
            'lines.*.notes' => ['nullable', 'string'],
        ]) + ['currency' => $request->input('currency', 'KES')];
    }

    private function formData(?SalesRecord $record = null): array
    {
        return [
            'record' => $record,
            'organizations' => Organization::orderBy('name')->get(),
            'farms' => Farm::orderBy('name')->get(),
            'customers' => SalesCustomer::where('is_active', true)->orderBy('name')->get(),
            'items' => SalesCatalogItem::where('is_active', true)->orderBy('name')->get(),
            'cropCycles' => CropCycle::orderBy('name')->get(),
            'cropHarvests' => CropHarvestRecord::latest('harvest_date')->limit(100)->get(),
            'animals' => LivestockAnimal::orderBy('animal_code')->get(),
            'animalGroups' => LivestockAnimalGroup::orderBy('name')->get(),
            'yields' => LivestockYieldRecord::latest('yield_date')->limit(100)->get(),
        ];
    }
}
