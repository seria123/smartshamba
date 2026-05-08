<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Http\Controllers\Concerns\PreparesDocumentRequests;
use App\Modules\Documents\Models\AttachmentCategory;
use App\Modules\Documents\Services\DocumentsAccessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttachmentCategoryController extends Controller
{
    use PreparesDocumentRequests;

    public function index(Request $request): View
    {
        $context = DocumentsAccessContext::forUser($request->user());
        $categories = AttachmentCategory::query()
            ->where(fn ($query) => $query->whereNull('organization_id')->orWhereIn('organization_id', $context->organizations()->pluck('id')))
            ->orderBy('sort_order')->orderBy('name')->get();

        return view('documents::categories.index', ['categories' => $categories]);
    }

    public function create(Request $request): View
    {
        return view('documents::categories.create', $this->prepare($request) + ['category' => new AttachmentCategory()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        DocumentsAccessContext::forUser($request->user())->filters($data);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        AttachmentCategory::create($data);

        return redirect()->route('documents.categories.index')->with('status', 'Category created.');
    }

    public function edit(Request $request, AttachmentCategory $category): View
    {
        $this->authorizeCategory($request, $category);

        return view('documents::categories.edit', $this->prepare($request) + ['category' => $category]);
    }

    public function update(Request $request, AttachmentCategory $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $category->update($data);

        return redirect()->route('documents.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Request $request, AttachmentCategory $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);
        abort_if($category->is_system, 422, 'System categories cannot be deleted.');
        $category->delete();

        return redirect()->route('documents.categories.index')->with('status', 'Category deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'organization_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['is_active' => false, 'sort_order' => 0];
    }
}
