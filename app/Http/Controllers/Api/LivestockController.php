<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livestock;
use App\Models\LivestockType;
use App\Services\LivestockTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockController extends Controller
{
    /**
     * Livestock tracking service instance.
     */
    protected $trackingService;

    /**
     * Create a new controller instance.
     */
    public function __construct(LivestockTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }
    public function index(Request $request)
    {
        $query = Livestock::with('type')->where('user_id', Auth::id());

        if ($request->has('type_id')) {
            $query->where('livestock_type_id', $request->type_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tag_number', 'like', "%{$search}%");
            });
        }

        $livestock = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $livestock,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'farm_id' => 'nullable|exists:farms,id',
            'tag_number' => 'nullable|string|unique:livestock,tag_number',
            'name' => 'nullable|string|max:255',
            'date_acquired' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'parent_id' => 'nullable|exists:livestock,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $livestock = Livestock::create($validated);
        
        // Assign tracking ID in format KE-{farm_code}-{year}-{serial}
        $this->trackingService->assignTrackingId($livestock);

        return response()->json([
            'success' => true,
            'message' => 'Livestock created successfully',
            'data' => $livestock->load('type'),
        ], 201);
    }

    public function show(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        return response()->json([
            'success' => true,
            'data' => $livestock->load(['type', 'diseases.disease', 'parent', 'offspring']),
        ]);
    }

    public function update(Request $request, Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        $validated = $request->validate([
            'livestock_type_id' => 'sometimes|exists:livestock_types,id',
            'farm_id' => 'nullable|exists:farms,id',
            'tag_number' => 'sometimes|string|unique:livestock,tag_number,'.$livestock->id,
            'name' => 'nullable|string|max:255',
            'date_acquired' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'status' => 'sometimes|in:healthy,sick,sold,dead',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $livestock->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livestock updated successfully',
            'data' => $livestock->load('type'),
        ]);
    }

    public function destroy(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        $livestock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livestock deleted successfully',
        ]);
    }

    public function summary()
    {
        $userId = Auth::id();
        $types = LivestockType::withCount([
            'livestock as total' => function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereIn('status', [Livestock::STATUS_HEALTHY, Livestock::STATUS_SICK]);
            },
            'livestock as healthy' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', Livestock::STATUS_HEALTHY);
            },
            'livestock as sick' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', Livestock::STATUS_SICK);
            },
            'livestock as sold' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', Livestock::STATUS_SOLD);
            },
            'livestock as dead' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', Livestock::STATUS_DEAD);
            },
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    protected function authorizeOwnership(Livestock $livestock): void
    {
        if ($livestock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
