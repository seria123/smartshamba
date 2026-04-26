<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livestock;
use App\Models\LivestockDisease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiseaseController extends Controller
{
    public function index(Request $request)
    {
        $query = LivestockDisease::query();

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('species')) {
            $query->where('species', $request->species);
        }

        $diseases = $query->with(['livestock', 'disease', 'treatedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $diseases,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'required|exists:livestock,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'cause' => 'required|string|max:255',
            'symptoms' => 'required|string',
            'transmission' => 'nullable|string',
            'prevention' => 'nullable|string',
            'treatment' => 'nullable|string',
            'status' => 'sometimes|in:active,treated,chronic',
            'diagnosed_date' => 'nullable|date',
            'mortality_rate' => 'nullable|numeric|min:0|max:100',
            'severity' => 'sometimes|in:low,medium,high',
        ]);

        $validated['status'] = $validated['status'] ?? LivestockDisease::STATUS_ACTIVE;
        $validated['diagnosed_date'] = $validated['diagnosed_date'] ?? now();

        $disease = LivestockDisease::create($validated);

        // Update livestock status to sick
        $livestock = Livestock::find($validated['livestock_id']);
        $livestock->markAsSick();

        return response()->json([
            'success' => true,
            'message' => 'Livestock disease record created successfully',
            'data' => $disease->load(['livestock', 'disease', 'treatedBy']),
        ], 201);
    }

    public function show(LivestockDisease $livestockDisease)
    {
        return response()->json([
            'success' => true,
            'data' => $livestockDisease->load(['livestock', 'disease', 'treatedBy']),
        ]);
    }

    public function update(Request $request, LivestockDisease $livestockDisease)
    {
        $validated = $request->validate([
            'livestock_id' => 'sometimes|exists:livestock,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'name' => 'sometimes|string|max:255',
            'species' => 'sometimes|string|max:255',
            'cause' => 'sometimes|string|max:255',
            'symptoms' => 'sometimes|string',
            'transmission' => 'nullable|string',
            'prevention' => 'nullable|string',
            'treatment' => 'nullable|string',
            'status' => 'sometimes|in:active,treated,chronic',
            'diagnosed_date' => 'nullable|date',
            'treated_date' => 'nullable|date',
            'treated_by' => 'nullable|exists:users,id',
            'mortality_rate' => 'nullable|numeric|min:0|max:100',
            'severity' => 'sometimes|in:low,medium,high',
        ]);

        $livestockDisease->update($validated);

        // Update livestock status if needed
        if (isset($validated['status']) && $validated['status'] === LivestockDisease::STATUS_TREATED) {
            $livestockDisease->livestock->markAsHealthy();
        }

        return response()->json([
            'success' => true,
            'message' => 'Livestock disease record updated successfully',
            'data' => $livestockDisease->fresh(['livestock', 'disease', 'treatedBy']),
        ]);
    }

    public function destroy(LivestockDisease $livestockDisease)
    {
        // Check if this is the only record for this livestock
        $otherDiseases = LivestockDisease::where('livestock_id', $livestockDisease->livestock_id)
            ->where('id', '!=', $livestockDisease->id)
            ->where('status', 'active')
            ->count();

        $livestockDisease->delete();

        // If no other active diseases, mark livestock as healthy
        if ($otherDiseases === 0) {
            $livestockDisease->livestock->markAsHealthy();
        }

        return response()->json([
            'success' => true,
            'message' => 'Livestock disease record deleted successfully',
        ]);
    }

    public function diagnoseLivestock(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'required|exists:livestock,id',
            'disease_id' => 'required|exists:diseases,id',
            'diagnosed_date' => 'nullable|date',
        ]);

        // Get disease information from the Disease model
        $disease = \App\Models\Disease::find($validated['disease_id']);

        $caseData = [
            'livestock_id' => $validated['livestock_id'],
            'disease_id' => $validated['disease_id'],
            'name' => $disease->name,
            'species' => 'Unknown', // This might need to be determined based on livestock type
            'cause' => $disease->description ?? 'Unknown',
            'symptoms' => $disease->symptoms ?? 'Unknown',
            'transmission' => 'Unknown',
            'prevention' => $disease->prevention ?? 'Unknown',
            'treatment' => $disease->treatment ?? 'Unknown',
            'status' => LivestockDisease::STATUS_ACTIVE,
            'diagnosed_date' => $validated['diagnosed_date'] ?? now(),
            'severity' => $disease->severity ?? 'medium',
        ];

        $case = LivestockDisease::create($caseData);

        $livestock = Livestock::find($validated['livestock_id']);
        $livestock->markAsSick();

        return response()->json([
            'success' => true,
            'message' => 'Disease diagnosed successfully',
            'data' => $case->load(['livestock', 'disease']),
        ], 201);
    }

    public function treatLivestock(Request $request, LivestockDisease $livestockDisease)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $livestockDisease->markAsTreated(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Livestock treated successfully',
            'data' => $livestockDisease->fresh(['livestock', 'disease']),
        ]);
    }

    public function highSeverity()
    {
        $diseases = LivestockDisease::where('severity', 'high')
            ->with(['livestock', 'disease', 'treatedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $diseases,
        ]);
    }

    public function contagious()
    {
        $diseases = LivestockDisease::whereNotNull('transmission')
            ->where('status', 'active')
            ->with(['livestock', 'disease', 'treatedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $diseases,
        ]);
    }
}
