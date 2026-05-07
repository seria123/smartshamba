<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Core\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    public function index(): View { return view('assets::categories.index', ['categories' => AssetCategory::with(['organization', 'parent'])->withCount('assets')->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('assets::categories.form', $this->formData()); }
    public function show(AssetCategory $category): View { return view('assets::categories.show', ['category' => $category->load(['organization', 'parent', 'children'])->loadCount('assets')]); }
    public function edit(AssetCategory $category): View { return view('assets::categories.form', $this->formData($category)); }

    public function store(Request $request): RedirectResponse
    {
        AssetCategory::query()->create($this->validated($request) + ['created_by' => $request->user()?->id]);
        return redirect()->route('assets.categories.index')->with('status', 'Asset category created.');
    }

    public function update(Request $request, AssetCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category) + ['updated_by' => $request->user()?->id]);
        return redirect()->route('assets.categories.show', $category)->with('status', 'Asset category updated.');
    }

    public function deactivate(Request $request, AssetCategory $category): RedirectResponse
    {
        $category->update(['status' => 'inactive', 'updated_by' => $request->user()?->id]);
        return redirect()->route('assets.categories.show', $category)->with('status', 'Asset category deactivated.');
    }

    private function formData(?AssetCategory $category = null): array
    {
        return [
            'category' => $category,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'parents' => AssetCategory::query()->when($category, fn ($query) => $query->whereKeyNot($category->id))->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?AssetCategory $category = null): array
    {
        $organizationId = $request->filled('organization_id') ? $request->integer('organization_id') : null;
        $slug = Str::slug($request->input('slug') ?: $request->input('name'));

        $data = $request->validate([
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('asset_categories', 'slug')->where('organization_id', $organizationId)->ignore($category?->id)],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:asset_categories,id'],
            'is_system' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);

        $data['organization_id'] = $organizationId;
        $data['slug'] = $slug;
        $data['is_system'] = (bool) ($data['is_system'] ?? false);

        return $data;
    }
}
