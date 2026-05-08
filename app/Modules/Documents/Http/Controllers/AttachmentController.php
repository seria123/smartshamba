<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Http\Controllers\Concerns\PreparesDocumentRequests;
use App\Modules\Documents\Models\FarmAttachment;
use App\Modules\Documents\Services\AttachmentSourceResolver;
use App\Modules\Documents\Services\AttachmentStorageService;
use App\Modules\Documents\Services\AttachmentSummaryService;
use App\Modules\Documents\Services\DocumentsAccessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttachmentController extends Controller
{
    use PreparesDocumentRequests;

    public function index(Request $request, AttachmentSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        $attachments = $summary->filtered($prepared['context'], $prepared['filters'])->latest()->paginate(20)->withQueryString();

        return view('documents::attachments.index', $prepared + ['attachments' => $attachments]);
    }

    public function create(Request $request): View
    {
        return view('documents::attachments.create', $this->prepare($request) + ['attachment' => new FarmAttachment()]);
    }

    public function store(Request $request, AttachmentStorageService $storage, AttachmentSourceResolver $sources): RedirectResponse
    {
        $data = $this->validated($request, true);
        $context = DocumentsAccessContext::forUser($request->user());
        $context->filters($data);
        $fileData = $storage->store($request->file('file'), (int) $data['organization_id'], $data['farm_id'] ? (int) $data['farm_id'] : null);

        $attachment = FarmAttachment::create($data + $fileData + [
            'uploaded_by' => $request->user()->id,
            'visibility' => 'private',
            'status' => 'active',
            'action_url' => $sources->actionUrl($data['attachable_module'] ?? null, $data['attachable_type'] ?? null, $data['attachable_id'] ?? null),
        ]);

        return redirect()->route('documents.attachments.show', $attachment)->with('status', 'Attachment uploaded.');
    }

    public function show(Request $request, FarmAttachment $attachment): View
    {
        $this->authorizeAttachment($request, $attachment);

        return view('documents::attachments.show', ['attachment' => $attachment->load(['category', 'farm', 'uploader'])]);
    }

    public function edit(Request $request, FarmAttachment $attachment): View
    {
        $this->authorizeAttachment($request, $attachment);

        return view('documents::attachments.edit', $this->prepare($request) + ['attachment' => $attachment]);
    }

    public function update(Request $request, FarmAttachment $attachment, AttachmentSourceResolver $sources): RedirectResponse
    {
        $this->authorizeAttachment($request, $attachment);
        $data = $this->validated($request, false);
        DocumentsAccessContext::forUser($request->user())->filters($data);
        $attachment->update($data + ['action_url' => $sources->actionUrl($data['attachable_module'] ?? null, $data['attachable_type'] ?? null, $data['attachable_id'] ?? null)]);

        return redirect()->route('documents.attachments.show', $attachment)->with('status', 'Attachment updated.');
    }

    public function destroy(Request $request, FarmAttachment $attachment): RedirectResponse
    {
        $this->authorizeAttachment($request, $attachment);
        $attachment->update(['deleted_by' => $request->user()->id, 'status' => 'archived']);
        $attachment->delete();

        return redirect()->route('documents.attachments.index')->with('status', 'Attachment removed.');
    }

    private function validated(Request $request, bool $requireFile): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer'],
            'farm_id' => ['nullable', 'integer'],
            'attachment_category_id' => ['nullable', 'integer', 'exists:attachment_categories,id'],
            'attachable_module' => ['nullable', 'string', 'max:80'],
            'attachable_type' => ['nullable', 'string', 'max:120'],
            'attachable_id' => ['nullable', 'integer'],
            'attachable_label' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => [$requireFile ? 'required' : 'nullable', 'file', 'max:10240', 'mimes:'.implode(',', AttachmentStorageService::ALLOWED_EXTENSIONS)],
        ]);
    }
}
