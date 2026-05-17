<?php

namespace App\Http\Controllers;

use App\Models\CropAnalysis;
use App\Models\CropAnalysisImage;
use App\Models\CropCycle;
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
        $query = CropAnalysis::with(['field', 'user', 'cropCycle', 'cropCycle.crop'])
            ->orderBy('created_at', 'desc');

        if (! Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        // Filter by crop_cycle if provided
        if (request('crop_cycle_id')) {
            $query->where('crop_cycle_id', request('crop_cycle_id'));
        }

        $analyses = $query->paginate(10);

        // Get crop cycles for filter dropdown
        $cropCycles = Auth::user()->isAdmin()
            ? CropCycle::all()
            : CropCycle::whereHas('field', fn ($q) => $q->where('user_id', Auth::id()))->get();

        return view('crop_analysis.index', compact('analyses', 'cropCycles'));
    }

    // ➕ CREATE FORM
    public function create()
    {
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field', fn ($q) => $q->where('user_id', Auth::id()))
            ->orWhereHas('farm', fn ($q) => $q->where('user_id', Auth::id()))
            ->with(['crop', 'field'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('crop_analysis.create', compact('fields', 'cropCycles'));
    }

    // 🚀 STORE (MAIN ENTRY POINT FOR ANALYSIS)
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:10240',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
        ]);

        try {
            $imageFiles = $request->file('images');
            if (! $imageFiles || count($imageFiles) === 0) {
                throw new \Exception('At least one image is required');
            }

            // Derive field_id from crop_cycle if not provided (for weather context)
            $fieldId = $request->field_id;
            $cropCycleId = $request->crop_cycle_id;
            if (! $fieldId && $cropCycleId) {
                $cropCycle = CropCycle::find($cropCycleId);
                if ($cropCycle) {
                    $fieldId = $cropCycle->field_id;
                }
            }

             // Process the first image for AI analysis
            $primaryImage = $imageFiles[0];
            $path = $this->storeImage($primaryImage, 'temp-analyses');
            $analysisResult = $this->analysisService->analyzeCrop($path);
             
            // Determine severity based on confidence and disease detection
            $severity = 'low';
            $diseaseDetected = isset($analysisResult['disease']) ? $analysisResult['disease'] : 'No disease detected';
            if (str_contains(strtolower($diseaseDetected), 'blight') || 
                str_contains(strtolower($diseaseDetected), 'rot') || 
                str_contains(strtolower($diseaseDetected), 'wilt')) {
                $severity = 'high';
            } elseif (str_contains(strtolower($diseaseDetected), 'mildew') || 
                     str_contains(strtolower($diseaseDetected), 'infestation') ||
                     str_contains(strtolower($diseaseDetected), 'mites') ||
                     str_contains(strtolower($diseaseDetected), 'aphid') ||
                     str_contains(strtolower($diseaseDetected), 'whitefly')) {
                $severity = 'medium';
            }
             
            // Create analysis record from the result
            $analysis = CropAnalysis::create([
                'field_id' => $fieldId,
                'crop_cycle_id' => $cropCycleId,
                'user_id' => Auth::id(),
                'image_path' => $path,
                'diagnosis' => isset($analysisResult['crop']) ? $analysisResult['crop'] : 'Unknown',
                'description' => "AI analysis detected: {$diseaseDetected} with ".(isset($analysisResult['confidence']) ? $analysisResult['confidence'] : 0)."% confidence.",
                'severity' => $severity,
                'recommendation' => $this->getRecommendationForDisease($diseaseDetected),
                'detected_issues' => [
                    [
                        'type' => strtolower(str_replace(' ', '_', $diseaseDetected)),
                        'description' => $diseaseDetected,
                        'confidence' => isset($analysisResult['confidence']) ? $analysisResult['confidence'] : 0
                    ]
                ],
                'confidence_score' => isset($analysisResult['confidence']) ? $analysisResult['confidence'] : 0,
                'status' => 'analyzed',
                'raw_data' => json_encode(isset($analysisResult['raw']) ? $analysisResult['raw'] : null),
            ]);

            // Store additional images
            if (count($imageFiles) > 1) {
                foreach (array_slice($imageFiles, 1) as $index => $image) {
                    $path = $this->storeImage($image, 'crop-analyses');
                    CropAnalysisImage::create([
                        'crop_analysis_id' => $analysis->id,
                        'image_path' => $path,
                        'order' => $index + 1,
                        'label' => 'Additional Image '.($index + 2),
                    ]);
                }
            }

            return redirect()
                ->route('crop_analyses.show', $analysis->id)
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
        $cropAnalysis = CropAnalysis::with(['field', 'user', 'cropCycle', 'cropCycle.crop', 'images'])
            ->findOrFail($id);

        // Authorization check
        if ($cropAnalysis->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('crop_analysis.show', compact('cropAnalysis'));
    }

    // ✔ MARK AS REVIEWED
    public function markReviewed(CropAnalysis $cropAnalysis)
    {
        $this->authorizeAccess($cropAnalysis);

        if ($cropAnalysis->status !== 'reviewed') {
            $cropAnalysis->update(['status' => 'reviewed']);
        }

        return back()->with('success', 'Analysis marked as reviewed.');
    }

    // 🗑 DELETE
    public function destroy(CropAnalysis $cropAnalysis)
    {
        $this->authorizeAccess($cropAnalysis);

        // Delete primary image
        if ($cropAnalysis->image_path) {
            Storage::disk('public')->delete($cropAnalysis->image_path);
        }

        // Delete additional images
        foreach ($cropAnalysis->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $cropAnalysis->delete();

        return redirect()
            ->route('crop_analyses.index')
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

    // Helper: Store uploaded image
    private function storeImage($image, string $directory): string
    {
        return $image->store($directory, 'public');
    }

    /**
     * Get recommendation based on detected disease/condition
     */
    private function getRecommendationForDisease(string $disease): string
    {
        $diseaseLower = strtolower($disease);

        if (str_contains($diseaseLower, 'healthy') || str_contains($diseaseLower, 'no disease')) {
            return 'Continue regular maintenance and monitoring.';
        }

        if (str_contains($diseaseLower, 'blight')) {
            return 'Apply appropriate fungicide, improve air circulation, and remove affected plant parts.';
        }

        if (str_contains($diseaseLower, 'mildew')) {
            return 'Improve airflow, reduce humidity, and apply fungicide if necessary.';
        }

        if (str_contains($diseaseLower, 'rot') || str_contains($diseaseLower, 'wilt')) {
            return 'Improve drainage, avoid overwatering, and consider soil treatment.';
        }

        if (str_contains($diseaseLower, 'infestation') || 
            str_contains($diseaseLower, 'aphid') || 
            str_contains($diseaseLower, 'whitefly') ||
            str_contains($diseaseLower, 'mite')) {
            return 'Apply appropriate insecticide or biological control, and monitor regularly.';
        }

        if (str_contains($diseaseLower, 'deficiency')) {
            return 'Apply appropriate fertilizer based on soil test results.';
        }

        return 'Consult with an agricultural extension officer for specific treatment recommendations.';
    }

    // Helper: Authorize access to analysis
    private function authorizeAccess(CropAnalysis $analysis): void
    {
        if ($analysis->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }
}
