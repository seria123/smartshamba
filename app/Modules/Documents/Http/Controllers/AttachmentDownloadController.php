<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Http\Controllers\Concerns\PreparesDocumentRequests;
use App\Modules\Documents\Models\FarmAttachment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class AttachmentDownloadController extends Controller
{
    use PreparesDocumentRequests;

    public function download(Request $request, FarmAttachment $attachment): StreamedResponse
    {
        $this->authorizeAttachment($request, $attachment);
        $this->ensureFileExists($attachment);

        return Storage::disk($attachment->storage_disk)->download($attachment->storage_path, $attachment->original_filename);
    }

    public function preview(Request $request, FarmAttachment $attachment): Response
    {
        $this->authorizeAttachment($request, $attachment);
        $this->ensureFileExists($attachment);

        abort_unless(in_array($attachment->file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'webp'], true), 404);

        return response(Storage::disk($attachment->storage_disk)->get($attachment->storage_path), 200, [
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$attachment->original_filename.'"',
        ]);
    }

    private function ensureFileExists(FarmAttachment $attachment): void
    {
        if (! Storage::disk($attachment->storage_disk)->exists($attachment->storage_path)) {
            throw new NotFoundHttpException('Attachment file was not found.');
        }
    }
}
