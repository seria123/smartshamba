<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View { return view('inventory::categories.index', ['categories' => InventoryProductCategory::with(['organization', 'parent'])->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('inventory::categories.form', $this->formData()); }
    public function show(InventoryProductCategory $category): View { return view('inventory::categories.show', ['category' => $category->load(['organization', 'parent', 'products'])]); }
    public function edit(InventoryProductCategory $category): View { return view('inventory::categories.form', $this->formData($category)); }

    public function store(Request $request): RedirectResponse
    {
        InventoryProductCategory::query()->create($this->validated($request));
        return redirect()->route('inventory.categories.index')->with('status', 'Category created.');
    }

    public function update(Request $request, InventoryProductCategory $category): RedirectResponse
    {
        $category->update($this->validated($request));
        return redirect()->route('inventory.categories.show', $category)->with('status', 'Category updated.');
    }

    public function deactivate(InventoryProductCategory $category): RedirectResponse
    {
        $category->update(['status' => 'inactive']);
        return redirect()->route('inventory.categories.show', $category)->with('status', 'Category deactivated.');
    }

    private function formData(?InventoryProductCategory $category = null): array
    {
        return [
            'category' => $category,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'parents' => InventoryProductCategory::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'parent_id' => ['nullable', 'integer', 'exists:inventory_product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        return $data;
    }
}
