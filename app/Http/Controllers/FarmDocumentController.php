<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FarmDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FarmDocumentController extends Controller
{
    /**
     * Store a newly uploaded document for a farm.
     */
    public function store(Request $request, Farm $farm)
    {
        $this->authorizeOwnership($farm);

        $validated = $request->validate([
            'document' => 'required|file|max:10240', // 10MB max
            'document_type' => 'required|string|in:ownership,title_deed,tax_compliance,insurance,certification,other',
            'original_name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('document');
        $path = $file->store('farm-documents', 'public');

        $farm->documents()->create([
            'document_type' => $validated['document_type'],
            'file_path' => $path,
            'original_name' => $validated['original_name'] ?? $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display/download the specified document.
     */
    public function show(Request $request, Farm $farm, FarmDocument $document)
    {
        $this->authorizeOwnership($farm);

        if ($document->farm_id !== $farm->id) {
            abort(403, 'Unauthorized');
        }

        return Storage::disk('public')->response($document->file_path);
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Request $request, Farm $farm, FarmDocument $document)
    {
        $this->authorizeOwnership($farm);

        if ($document->farm_id !== $farm->id) {
            abort(403, 'Unauthorized');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Authorize farm ownership.
     */
    private function authorizeOwnership(Farm $farm): void
    {
        if ($farm->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }
}
