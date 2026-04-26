<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockWoundAnalysis;
use App\Services\LivestockWoundAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockWoundAnalysisController extends Controller
{
    protected LivestockWoundAnalysisService $analysisService;

    public function __construct(LivestockWoundAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Display a listing of wound analyses.
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $analyses = LivestockWoundAnalysis::with(['livestock', 'user'])
                ->latest()
                ->paginate(10);
        } else {
            $analyses = LivestockWoundAnalysis::with(['livestock', 'user'])
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(10);
        }

        return view('livestock.wounds.index', compact('analyses'));
    }

    /**
     * Show the form for creating a new wound analysis.
     */
    public function create()
    {
        $livestock = Livestock::where('user_id', Auth::id())
            ->where('status', '!=', 'dead')
            ->where('status', '!=', 'sold')
            ->orderBy('name')
            ->get();

        return view('livestock.wounds.create', compact('livestock'));
    }

    /**
     * Store a newly created wound analysis.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:10240|mimes:jpeg,png,jpg,gif,webp|dimensions:min_width=100,min_height=100',
            'livestock_id' => 'nullable|exists:livestock,id',
        ]);

        $analysis = $this->analysisService->analyzeWound(
            $validated['image'],
            $validated['livestock_id'] ?? null
        );

        return redirect()->route('wounds.show', $analysis)
            ->with('success', 'Wound analysis completed successfully!');
    }

    /**
     * Display the specified wound analysis.
     */
    public function show(LivestockWoundAnalysis $wound)
    {
        $this->authorizeOwnership($wound);

        return view('livestock.wounds.show', compact('wound'));
    }

    /**
     * Show the form for editing the specified wound analysis.
     */
    public function edit(LivestockWoundAnalysis $wound)
    {
        $this->authorizeOwnership($wound);

        return view('livestock.wounds.edit', compact('wound'));
    }

    /**
     * Update the specified wound analysis.
     */
    public function update(Request $request, LivestockWoundAnalysis $wound)
    {
        $this->authorizeOwnership($wound);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,analyzed,treated,healed',
        ]);

        $wound->update($validated);

        return redirect()->route('wounds.show', $wound)
            ->with('success', 'Wound analysis updated successfully.');
    }

    /**
     * Mark wound as treated.
     */
    public function markTreated(LivestockWoundAnalysis $wound)
    {
        $this->authorizeOwnership($wound);

        $wound->update([
            'status' => 'treated',
        ]);

        return redirect()->back()
            ->with('success', 'Wound marked as treated.');
    }

    /**
     * Mark wound as healed.
     */
    public function markHealed(LivestockWoundAnalysis $wound)
    {
        $this->authorizeOwnership($wound);

        $wound->update([
            'status' => 'healed',
        ]);

        return redirect()->back()
            ->with('success', 'Wound marked as healed.');
    }

    /**
     * Remove the specified wound analysis.
     */
    public function destroy(LivestockWoundAnalysis $wound)
    {
        $this->authorizeOwnership($wound);

        if ($wound->image_path) {
            \Storage::disk('public')->delete($wound->image_path);
        }

        $wound->delete();

        return redirect()->route('wounds.index')
            ->with('success', 'Wound analysis deleted successfully.');
    }

    /**
     * Get wound history for a specific livestock.
     */
    public function livestockHistory(Livestock $livestock)
    {
        if ($livestock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $analyses = LivestockWoundAnalysis::where('livestock_id', $livestock->id)
            ->latest()
            ->paginate(10);

        return view('livestock.wounds.history', compact('livestock', 'analyses'));
    }

    /**
     * Show high urgency wound cases.
     */
    public function highUrgency()
    {
        $analyses = LivestockWoundAnalysis::with(['livestock', 'user'])
            ->where('urgency', 'immediate')
            ->orWhere('urgency', 'urgent')
            ->latest()
            ->paginate(10);

        return view('livestock.wounds.index', compact('analyses'));
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
