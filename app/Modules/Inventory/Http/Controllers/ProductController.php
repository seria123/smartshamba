<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Http\Controllers\Concerns\ValidatesInventoryScope;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    use ValidatesInventoryScope;

    public function index(): View { return view('inventory::products.index', ['products' => InventoryProduct::with(['organization', 'category'])->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('inventory::products.form', $this->formData()); }
    public function show(InventoryProduct $product): View { return view('inventory::products.show', ['product' => $product->load(['organization', 'category', 'stockLots.warehouse'])]); }
    public function edit(InventoryProduct $product): View { return view('inventory::products.form', $this->formData($product)); }

    public function store(Request $request): RedirectResponse
    {
        InventoryProduct::query()->create($this->validated($request));
        return redirect()->route('inventory.products.index')->with('status', 'Product created.');
    }

    public function update(Request $request, InventoryProduct $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));
        return redirect()->route('inventory.products.show', $product)->with('status', 'Product updated.');
    }

    public function deactivate(InventoryProduct $product): RedirectResponse
    {
        $product->update(['status' => 'inactive']);
        return redirect()->route('inventory.products.show', $product)->with('status', 'Product deactivated.');
    }

    private function formData(?InventoryProduct $product = null): array
    {
        return [
            'product' => $product,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'categories' => InventoryProductCategory::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?InventoryProduct $product = null): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'category_id' => ['required', 'integer', 'exists:inventory_product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('inventory_products', 'code')->where('organization_id', $request->integer('organization_id'))->ignore($product?->id)],
            'sku' => ['nullable', 'string', 'max:255'],
            'product_type' => ['required', 'string', 'max:255'],
            'unit_of_measure' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'active_ingredient' => ['nullable', 'string', 'max:255'],
            'tracks_batch' => ['nullable', 'boolean'],
            'tracks_expiry' => ['nullable', 'boolean'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'default_unit_cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $this->ensureCategoryAvailableToOrganization((int) $data['category_id'], (int) $data['organization_id']);
        $data['tracks_batch'] = (bool) ($data['tracks_batch'] ?? false);
        $data['tracks_expiry'] = (bool) ($data['tracks_expiry'] ?? false);
        $data['reorder_level'] = $data['reorder_level'] ?? 0;
        $data['default_unit_cost'] = $data['default_unit_cost'] ?? 0;
        return $data;
    }
}
