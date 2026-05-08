<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Sales\Models\SalesCatalogItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesCatalogItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = SalesCatalogItem::with(['organization', 'farm'])
            ->when($request->input('category'), fn ($query, $category) => $query->where('category', $category))
            ->when($request->input('farm_id'), fn ($query, $id) => $query->where('farm_id', $id))
            ->orderBy('name')->paginate(20);

        return view('sales::catalog-items.index', ['items' => $items] + $this->formData());
    }

    public function create(): View { return view('sales::catalog-items.form', $this->formData()); }
    public function show(SalesCatalogItem $catalogItem): View { return view('sales::catalog-items.show', ['item' => $catalogItem->load(['organization', 'farm', 'lines.record'])]); }
    public function edit(SalesCatalogItem $catalogItem): View { return view('sales::catalog-items.form', $this->formData($catalogItem)); }

    public function store(Request $request): RedirectResponse
    {
        $item = SalesCatalogItem::query()->create($this->validated($request) + ['created_by' => $request->user()?->id]);
        return redirect()->route('sales.catalog-items.show', $item)->with('status', 'Catalog item created.');
    }

    public function update(Request $request, SalesCatalogItem $catalogItem): RedirectResponse
    {
        $catalogItem->update($this->validated($request) + ['updated_by' => $request->user()?->id]);
        return redirect()->route('sales.catalog-items.show', $catalogItem)->with('status', 'Catalog item updated.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['nullable', 'integer', 'exists:farms,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['crop_produce', 'livestock', 'animal_product', 'service', 'other'])],
            'unit' => ['required', Rule::in(['kg', 'g', 'bag', 'crate', 'bunch', 'litre', 'tray', 'piece', 'head', 'unit', 'other'])],
            'sku' => ['nullable', 'string', 'max:100'],
            'default_unit_price' => ['nullable', 'numeric', 'gte:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if (! empty($data['farm_id']) && ! Farm::where('id', $data['farm_id'])->where('organization_id', $data['organization_id'])->exists()) {
            throw ValidationException::withMessages(['farm_id' => 'The selected farm must belong to the selected organization.']);
        }

        return $data + ['currency' => $request->input('currency', 'KES'), 'is_active' => $request->boolean('is_active', true)];
    }

    private function formData(?SalesCatalogItem $item = null): array
    {
        return ['item' => $item, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get()];
    }
}
