<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Finance\Models\FinanceCostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceCostCategoryController extends Controller
{
    public function index(): View { return view('finance::categories.index', ['categories' => FinanceCostCategory::with(['organization', 'farm'])->orderBy('sort_order')->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('finance::categories.form', $this->formData()); }
    public function show(FinanceCostCategory $category): View { return view('finance::categories.show', ['category' => $category->load(['organization', 'farm'])]); }
    public function edit(FinanceCostCategory $category): View { return view('finance::categories.form', $this->formData($category)); }

    public function store(Request $request): RedirectResponse
    {
        FinanceCostCategory::query()->create($this->validated($request));
        return redirect()->route('finance.categories.index')->with('status', 'Cost category created.');
    }

    public function update(Request $request, FinanceCostCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));
        return redirect()->route('finance.categories.show', $category)->with('status', 'Cost category updated.');
    }

    public function deactivate(FinanceCostCategory $category): RedirectResponse
    {
        $category->update(['is_active' => false]);
        return redirect()->route('finance.categories.show', $category)->with('status', 'Cost category deactivated.');
    }

    private function formData(?FinanceCostCategory $category = null): array
    {
        return ['category' => $category, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get()];
    }

    private function validated(Request $request, ?FinanceCostCategory $category = null): array
    {
        return $request->validate([
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'farm_id' => ['nullable', 'integer', 'exists:farms,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('finance_cost_categories')->where('organization_id', $request->input('organization_id'))->where('farm_id', $request->input('farm_id'))->ignore($category?->id)],
            'code' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost_nature' => ['required', Rule::in(['variable', 'fixed', 'capital', 'overhead', 'other'])],
            'default_source_module' => ['nullable', Rule::in(['manual', 'labour', 'inventory', 'crop', 'livestock', 'irrigation', 'asset', 'maintenance', 'task', 'other'])],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['is_active' => $request->boolean('is_active'), 'sort_order' => $request->integer('sort_order')];
    }
}
