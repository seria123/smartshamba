<?php

namespace App\Http\Controllers;

use App\Models\CropAnalysis;
use App\Models\Field;
use App\Services\CropAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CropAnalysisController extends Controller
{
    protected CropAnalysisService $analysisService;

    public function __construct(CropAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    // 📊 LIST ANALYSES
    public function index()
    {
        $query = CropAnalysis::with(['field', 'user'])
            ->orderBy('created_at', 'desc');

        if (! Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $analyses = $query->paginate(10);

        return view('crop_analysis.index', compact('analyses'));
    }

    // ➕ CREATE FORM
    public function create()
    {
        $fields = Field::where('user_id', Auth::id())->get();

        return view('crop_analysis.create', compact('fields'));
    }

    // 🚀 STORE (MAIN ENTRY POINT FOR ANALYSIS)
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
            'field_id' => 'nullable|exists:fields,id',
        ]);

        try {
            $imageFile = $request->file('image');
            
            // Enhanced upload diagnostics
            Log::info('Upload diagnostics', [
                'hasFile' => $request->hasFile('image'),
                'fileValid' => $imageFile->isValid(),
                'originalName' => $imageFile->getClientOriginalName(),
                'mimeType' => $imageFile->getMimeType(),
                'size' => $imageFile->getSize(),
                'error' => $imageFile->getError(),
            ]);

            if (!$imageFile->isValid()) {
                throw new \Exception('Uploaded file is corrupted or invalid');
            }

            $analysis = $this->analysisService->analyze(
                $imageFile,
                $request->field_id
            );

            if (!$analysis || !$analysis->id) {
                throw new \Exception('Analysis failed - no result returned');
            }

            return redirect()
                ->route('crop_analysis.show', $analysis->id)
                ->with('success', 'Analysis completed successfully');

        } catch (\Exception $e) {
            Log::error('Crop analysis error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Analysis failed: '.$e->getMessage())
                ->with('error_details', 'Please try again with a clearer image. If the problem persists, check that API keys are configured.');
        }
    }

    // 👁 SHOW SINGLE ANALYSIS
    public function show($id)
    {
        $crop_analysis = CropAnalysis::with(['field', 'user'])->findOrFail($id);

        return view('crop_analysis.show', compact('crop_analysis'));
    }

    // ✔ MARK AS REVIEWED
    public function markReviewed(CropAnalysis $crop_analysis)
    {
        if ($crop_analysis->status !== 'reviewed') {
            $crop_analysis->update(['status' => 'reviewed']);
        }

        return back()->with('success', 'Analysis marked as reviewed.');
    }

    // 🗑 DELETE
    public function destroy(CropAnalysis $cropAnalysis)
    {
        if ($cropAnalysis->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($cropAnalysis->image_path) {
            Storage::disk('public')->delete($cropAnalysis->image_path);
        }

        $cropAnalysis->delete();

        return redirect()
            ->route('crop_analysis.index')
            ->with('success', 'Analysis deleted successfully.');
    }

    // 🌿 FIELD HISTORY
    public function fieldHistory(Field $field)
    {
        if ($field->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $analyses = CropAnalysis::where('field_id', $field->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('crop_analysis.field_history', compact('field', 'analyses'));
    }
}
