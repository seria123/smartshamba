<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockAnalysis;
use App\Models\LivestockType;
use App\Models\Farm;
use App\Models\Field;
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $livestock = Livestock::with('type')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        $types = LivestockType::withCount([
            'livestock as total' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->whereIn('status', ['healthy', 'sick']);
            },
            'livestock as healthy' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('status', 'healthy');
            },
            'livestock as sick' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('status', 'sick');
            },
        ])->get();

        return view('livestock.index', compact('livestock', 'types'));
    }

    public function create()
    {
        $types = LivestockType::all();
        $farms = Farm::all();
        $fields = Field::all();

        return view('livestock.create', compact('types', 'farms', 'fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name' => 'nullable|string|max:255',
            'tag_number' => 'nullable|string|unique:livestock,tag_number',
            'date_acquired' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $livestock = Livestock::create($validated);
        
        // Assign tracking ID in format KE-{farm_code}-{year}-{serial}
        $this->trackingService->assignTrackingId($livestock);

        return redirect()->route('livestock.show', $livestock)
            ->with('success', 'Livestock created successfully');
    }

    public function show(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        $livestock->load([
            'type',
            'farm',
            'currentLocation.field',
            'currentLocation.farm',
        ])->loadCount([
            'locations',
            'movements',
        ]);

        // Get recent analyses (last 5)
        $analyses = $livestock->analyses()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livestock.show', compact('livestock', 'analyses'));
    }

    public function edit(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);
        $types = LivestockType::all();
        $farms = Farm::all();
        $fields = Field::all();

        return view('livestock.edit', compact('livestock', 'types', 'farms', 'fields'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        $this->authorize('update', $livestock);

        $validated = $request->validate([
            'livestock_type_id' => 'sometimes|exists:livestock_types,id',
            'name' => 'nullable|string|max:255',
            'tag_number' => 'sometimes|string|unique:livestock,tag_number,'.$livestock->id,
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

        return redirect()->route('livestock.show', $livestock)
            ->with('success', 'Livestock updated successfully');
    }

    public function destroy(Livestock $livestock)
    {
        $this->authorize('delete', $livestock);
        $livestock->delete();

        return redirect()->route('livestock.index')
            ->with('success', 'Livestock deleted successfully');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display location history for a specific livestock.
     */
    public function locationHistory(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        $locations = $livestock->locations()
            ->with(['field', 'farm'])
            ->paginate(20);

        return view('livestock.locations.history', compact('livestock', 'locations'));
    }

    /**
     * Display current location of a livestock.
     */
    public function currentLocation(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        $currentLocation = $livestock->currentLocation()
            ->with(['field', 'farm'])
            ->first();

        return view('livestock.locations.current', compact('livestock', 'currentLocation'));
    }

     /**
      * Show form to add a new location record (move livestock).
      */
      public function createLocation(Livestock $livestock)
      {
          $this->authorizeOwnership($livestock);

          $fields = Field::where('farm_id', $livestock->farm_id)
              ->orWhere('farm_id', null)
              ->get();

          // Get initial GPS from livestock's current farm if available
          $initialLat = null;
          $initialLng = null;
          if ($livestock->farm && $livestock->farm->gps_latitude && $livestock->farm->gps_longitude) {
              $initialLat = $livestock->farm->gps_latitude;
              $initialLng = $livestock->farm->gps_longitude;
          }

          return view('livestock.locations.create', compact('livestock', 'fields', 'initialLat', 'initialLng'));
      }

      /**
       * Store a new location record (move livestock).
       */
      public function storeLocation(Request $request, Livestock $livestock)
      {
          $this->authorizeOwnership($livestock);

          $validated = $request->validate([
              'field_id' => 'nullable|exists:fields,id',
              'farm_id' => 'nullable|exists:farms,id',
              'gps_latitude' => 'nullable|numeric|between:-90,90',
              'gps_longitude' => 'nullable|numeric|between:-180,180',
              'location_type' => 'required|in:field,farm,pasture,barn,transport,sick_bay,other',
              'movement_type' => 'required|in:grazing,resting,feeding,transport,treatment,inspection,birth,other',
              'notes' => 'nullable|string',
          ]);

          // Auto-populate GPS from field or farm if not provided
          if (empty($validated['gps_latitude']) || empty($validated['gps_longitude'])) {
              if (!empty($validated['field_id'])) {
                  $field = Field::find($validated['field_id']);
                  if ($field && $field->gps_latitude && $field->gps_longitude) {
                      $validated['gps_latitude'] = $field->gps_latitude;
                      $validated['gps_longitude'] = $field->gps_longitude;
                  }
              } elseif (!empty($validated['farm_id'])) {
                  $farm = Farm::find($validated['farm_id']);
                  if ($farm && $farm->gps_latitude && $farm->gps_longitude) {
                      $validated['gps_latitude'] = $farm->gps_latitude;
                      $validated['gps_longitude'] = $farm->gps_longitude;
                  }
              }
          }

          // Close previous active location if exists
          $livestock->locations()
              ->active()
              ->update(['left_at' => now()]);

          // Create new location record
          $livestock->locations()->create($validated);

          // Create movement record if farm or field changed
          if ($request->filled('field_id') || $request->filled('farm_id')) {
              $previousMovement = $livestock->movements()->latest()->first();

              $movementData = [
                  'livestock_id' => $livestock->id,
                  'user_id' => Auth::id(),
                  'movement_type' => $request->movement_type,
                  'movement_date' => now(),
                  'reason' => $request->notes,
                  'metadata' => [
                      'gps_latitude' => $validated['gps_latitude'] ?? null,
                      'gps_longitude' => $validated['gps_longitude'] ?? null,
                  ],
              ];

              if ($previousMovement) {
                  $movementData['from_farm_id'] = $previousMovement->to_farm_id;
                  $movementData['from_field_id'] = $previousMovement->to_field_id;
              } else {
                  $movementData['from_farm_id'] = $livestock->farm_id;
                  $movementData['from_field_id'] = null;
              }

              $movementData['to_farm_id'] = $request->farm_id ?? $livestock->farm_id;
              $movementData['to_field_id'] = $request->field_id;

              $livestock->movements()->create($movementData);

              // Update livestock's primary farm if changed
              if ($request->filled('farm_id') && $request->farm_id != $livestock->farm_id) {
                  $livestock->update(['farm_id' => $request->farm_id]);
              }
          }

          return redirect()->route('livestock.locations.index', $livestock)
              ->with('success', 'Location recorded successfully');
      }

      /**
       * Display movement history for a livestock.
       */
      public function movementHistory(Livestock $livestock)
      {
          $this->authorizeOwnership($livestock);

          $movements = $livestock->movements()
              ->with(['fromFarm', 'toFarm', 'fromField', 'toField', 'user'])
              ->paginate(20);

          return view('livestock.movements.index', compact('livestock', 'movements'));
      }

      /**
       * Display grazing patterns and analytics.
       */
      public function grazingPatterns(Livestock $livestock, Request $request)
      {
          $this->authorizeOwnership($livestock);

          $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
          $endDate = $request->input('end_date', now()->toDateString());

          $patterns = $livestock->locations()
              ->whereBetween('entered_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
              ->whereNotNull('duration_minutes')
              ->selectRaw('field_id, DATE(entered_at) as date, SUM(duration_minutes) as total_minutes, COUNT(*) as visit_count')
              ->groupBy('field_id', 'date')
              ->orderBy('date', 'desc')
              ->get();

          $totalDuration = $patterns->sum('total_minutes');
          $totalVisits = $patterns->sum('visit_count');

          return view('livestock.grazing.patterns', compact(
              'livestock',
              'patterns',
              'totalDuration',
              'totalVisits',
              'startDate',
              'endDate'
          ));
      }
}
