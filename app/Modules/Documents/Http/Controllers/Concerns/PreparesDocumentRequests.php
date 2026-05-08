<?php

namespace App\Modules\Documents\Http\Controllers\Concerns;

use App\Modules\Documents\Models\AttachmentCategory;
use App\Modules\Documents\Models\FarmAttachment;
use App\Modules\Documents\Services\DocumentsAccessContext;
use Illuminate\Http\Request;

trait PreparesDocumentRequests
{
    private function prepare(Request $request): array
    {
        $validated = $request->validate([
            'organization_id' => ['nullable', 'integer'],
            'farm_id' => ['nullable', 'integer'],
            'attachment_category_id' => ['nullable', 'integer'],
            'attachable_module' => ['nullable', 'string', 'max:80'],
            'attachable_type' => ['nullable', 'string', 'max:120'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'uploaded_by' => ['nullable', 'integer'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);
        $context = DocumentsAccessContext::forUser($request->user());
        $filters = $context->filters($validated);

        return [
            'context' => $context,
            'filters' => $filters,
            'organizations' => $context->organizations(),
            'farms' => $context->farms($filters['organization_id'] ?? null),
            'categories' => AttachmentCategory::query()->where(fn ($query) => $query->whereNull('organization_id')->orWhereIn('organization_id', $context->organizations()->pluck('id')))->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }

    private function authorizeAttachment(Request $request, FarmAttachment $attachment): DocumentsAccessContext
    {
        $context = DocumentsAccessContext::forUser($request->user());
        abort_unless($context->applyScope(FarmAttachment::query(), 'farm_attachments')->whereKey($attachment->id)->exists(), 403);

        return $context;
    }

    private function authorizeCategory(Request $request, AttachmentCategory $category): DocumentsAccessContext
    {
        $context = DocumentsAccessContext::forUser($request->user());
        if ($category->organization_id) {
            abort_unless($context->organizations()->contains('id', $category->organization_id), 403);
        }

        return $context;
    }
}
