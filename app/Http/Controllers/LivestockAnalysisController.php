<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockAnalysis;
use App\Services\LivestockAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LivestockAnalysisController extends Controller
{
    protected LivestockAnalysisService $analysisService;

    public function __construct(LivestockAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admins see all analyses, regular users only see their own
        if (Auth::user()->isAdmin()) {
            $analyses = LivestockAnalysis::with(['livestock', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $analyses = LivestockAnalysis::with(['livestock', 'user'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livestock_analysis.index', compact('analyses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $livestock = Livestock::where('user_id', Auth::id())->get();

        return view('livestock_analysis.create', compact('livestock'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:10240|mimes:jpeg,png,jpg,gif,webp|dimensions:min_width=100,min_height=100',
            'livestock_id' => 'nullable|exists:livestock,id',
        ]);

        $analysis = $this->analysisService->analyze(
            $validated['image'],
            $validated['livestock_id'] ?? null
        );

        // If livestock_id was provided and analysis detected a disease, create a disease record
        if (isset($validated['livestock_id']) && $analysis->diagnosis !== 'Healthy') {
            $this->createDiseaseRecordFromAnalysis($analysis);
        }

        return redirect()->route('livestock_analysis.show', $analysis)
            ->with('success', 'Livestock analysis completed successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $analysis = LivestockAnalysis::find($id);

        if (! $analysis) {
            return redirect()->route('livestock_analysis.index')
                ->with('error', 'Analysis not found.');
        }

        // Check authorization
        if ($analysis->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('livestock_analysis.show', compact('analysis'));
    }

    /**
     * Mark analysis as reviewed.
     */
    public function markReviewed(LivestockAnalysis $livestockAnalysis)
    {
        if ($livestockAnalysis->status !== 'reviewed') {
            $livestockAnalysis->status = 'reviewed';
            $livestockAnalysis->save();
        }

        return redirect()->back()->with('success', 'Analysis marked as reviewed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LivestockAnalysis $livestockAnalysis)
    {
        if ($livestockAnalysis->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Delete the image file
        if ($livestockAnalysis->image_path) {
            \Storage::disk('public')->delete($livestockAnalysis->image_path);
        }

        $livestockAnalysis->delete();

        return redirect()->route('livestock_analysis.index')
            ->with('success', 'Analysis deleted successfully.');
    }

    /**
     * Get analysis history for a specific livestock.
     */
    public function livestockHistory(Livestock $livestock)
    {
        if ($livestock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $analyses = LivestockAnalysis::where('livestock_id', $livestock->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livestock_analysis.history', compact('livestock', 'analyses'));
    }

    /**
     * API endpoint for mobile app integration.
     */
    public function apiAnalyze(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:10240|mimes:jpeg,png,jpg,gif,webp|dimensions:min_width=100,min_height=100',
            'livestock_id' => 'nullable|integer',
            'device_id' => 'nullable|string',
        ]);

        $analysis = $this->analysisService->analyze(
            $validated['image'],
            $validated['livestock_id'] ?? null
        );

        // If livestock_id was provided and analysis detected a disease, create a disease record
        if (isset($validated['livestock_id']) && $analysis->diagnosis !== 'Healthy') {
            $this->createDiseaseRecordFromAnalysis($analysis);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $analysis->id,
                'diagnosis' => $analysis->diagnosis,
                'description' => $analysis->description,
                'severity' => $analysis->severity,
                'recommendation' => $analysis->recommendation,
                'confidence' => $analysis->confidence_score,
                'detected_issues' => $analysis->detected_issues,
                'created_at' => $analysis->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Create a disease record from analysis results.
     */
    protected function createDiseaseRecordFromAnalysis(LivestockAnalysis $analysis): void
    {
        try {
            $livestock = $analysis->livestock;

            if (! $livestock) {
                return;
            }

            // Find matching disease from master list or create custom entry
            $disease = \App\Models\Disease::firstOrCreate(
                ['name' => $analysis->diagnosis],
                [
                    'description' => $analysis->description,
                    'severity' => $analysis->severity ?? 'medium',
                    'symptoms' => $analysis->description,
                    'treatment' => $analysis->recommendation,
                    'prevention' => 'Implement biosecurity measures and regular health checks',
                    'is_contagious' => $this->isLikelyContagious($analysis->diagnosis),
                    'is_active' => true,
                ]
            );

            // Check if there's already an active disease record for this livestock
            $existingRecord = \App\Models\LivestockDisease::where('livestock_id', $livestock->id)
                ->where('status', 'active')
                ->where('name', $analysis->diagnosis)
                ->first();

            if (! $existingRecord) {
                \App\Models\LivestockDisease::create([
                    'livestock_id' => $livestock->id,
                    'disease_id' => $disease->id,
                    'name' => $analysis->diagnosis,
                    'species' => $livestock->type ?? 'Unknown',
                    'cause' => 'Detected via AI image analysis',
                    'symptoms' => $analysis->description,
                    'transmission' => $disease->is_contagious ? 'May be contagious' : 'Non-contagious',
                    'prevention' => $disease->prevention,
                    'treatment' => $analysis->recommendation,
                    'status' => 'active',
                    'diagnosed_date' => now(),
                    'severity' => $analysis->severity ?? 'medium',
                ]);

                // Update livestock status
                $livestock->markAsSick();
            }

            Log::info("LivestockAnalysis: Created disease record for {$analysis->diagnosis} on livestock {$livestock->id}");
        } catch (\Exception $e) {
            Log::error('LivestockAnalysis: Failed to create disease record - '.$e->getMessage());
        }
    }

    /**
     * Quick heuristic to determine if a disease is likely contagious.
     */
    protected function isLikelyContagious(string $diseaseName): bool
    {
        $contagiousKeywords = [
            'foot and mouth', 'anthrax', 'rift valley', 'brucellosis',
            'tuberculosis', 'pneumonia', 'mastitis', 'tick', 'parasite',
            'virus', 'bacterial', 'infection', 'fever', 'contagious',
        ];

        $diseaseLower = strtolower($diseaseName);

        foreach ($contagiousKeywords as $keyword) {
            if (str_contains($diseaseLower, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
