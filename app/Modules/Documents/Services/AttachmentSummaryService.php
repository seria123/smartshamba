<?php

namespace App\Modules\Documents\Services;

use App\Modules\Documents\Models\FarmAttachment;

class AttachmentSummaryService
{
    public function dashboard(DocumentsAccessContext $context, array $filters = []): array
    {
        $base = $this->filtered($context, $filters);

        return [
            'totalAttachments' => (clone $base)->where('status', 'active')->count(),
            'totalStorage' => (clone $base)->where('status', 'active')->sum('file_size_bytes'),
            'uploadsThisMonth' => (clone $base)->where('created_at', '>=', now()->startOfMonth())->count(),
            'recentUploads' => (clone $base)->with(['category', 'farm', 'uploader'])->latest()->limit(10)->get(),
            'byCategory' => $this->byCategory($context, $filters),
            'bySource' => $this->bySource($context, $filters),
        ];
    }

    public function filtered(DocumentsAccessContext $context, array $filters = [])
    {
        return $context->applyScope(FarmAttachment::query(), 'farm_attachments')
            ->with(['category', 'farm', 'uploader'])
            ->where('farm_attachments.status', 'active')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('farm_attachments.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_attachments.farm_id', $id))
            ->when($filters['attachment_category_id'] ?? null, fn ($query, $id) => $query->where('farm_attachments.attachment_category_id', $id))
            ->when($filters['attachable_module'] ?? null, fn ($query, $module) => $query->where('farm_attachments.attachable_module', $module))
            ->when($filters['attachable_type'] ?? null, fn ($query, $type) => $query->where('farm_attachments.attachable_type', $type))
            ->when($filters['mime_type'] ?? null, fn ($query, $mime) => $query->where('farm_attachments.mime_type', 'like', '%'.$mime.'%'))
            ->when($filters['uploaded_by'] ?? null, fn ($query, $id) => $query->where('farm_attachments.uploaded_by', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('farm_attachments.created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('farm_attachments.created_at', '<=', $date))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query->where('farm_attachments.title', 'like', '%'.$search.'%')->orWhere('farm_attachments.original_filename', 'like', '%'.$search.'%')));
    }

    public function byCategory(DocumentsAccessContext $context, array $filters = [])
    {
        return $this->filtered($context, $filters)
            ->leftJoin('attachment_categories', 'attachment_categories.id', '=', 'farm_attachments.attachment_category_id')
            ->selectRaw("coalesce(attachment_categories.name, 'Uncategorized') as label, count(*) as total, sum(farm_attachments.file_size_bytes) as size")
            ->groupBy('attachment_categories.name')
            ->orderByDesc('total')
            ->get();
    }

    public function bySource(DocumentsAccessContext $context, array $filters = [])
    {
        return $this->filtered($context, $filters)
            ->selectRaw("coalesce(farm_attachments.attachable_module, 'general') as module, coalesce(farm_attachments.attachable_type, 'general') as type, count(*) as total, sum(farm_attachments.file_size_bytes) as size")
            ->groupBy('farm_attachments.attachable_module', 'farm_attachments.attachable_type')
            ->orderByDesc('total')
            ->get();
    }
}
