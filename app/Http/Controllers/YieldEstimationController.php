<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Farmer;
use App\Models\Field;
use App\Models\YieldEstimation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YieldEstimationController extends Controller
{
    public function index()
    {
        $query = YieldEstimation::with(['crop', 'farmer', 'field'])
            ->orderBy('created_at', 'desc');

        if (! Auth::user()->isAdmin()) {
            $query->whereHas('farmer', fn ($q) => $q->where('user_id', Auth::id()));
        }

        if (request('search')) {
            $query->whereHas('crop', fn ($q) => $q->where('name', 'like', '%'.request('search').'%'));
        }

        if (request('crop_id')) {
            $query->where('crop_id', request('crop_id'));
        }

        if (request('season')) {
            $query->where('season', request('season'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $query = $query->paginate(15)->withQueryString();

        $stats = [
            'total_estimations' => YieldEstimation::count(),
            'planned' => YieldEstimation::where('status', 'planned')->count(),
            'in_progress' => YieldEstimation::where('status', 'in_progress')->count(),
            'harvested' => YieldEstimation::where('status', 'harvested')->count(),
            'failed' => YieldEstimation::where('status', 'failed')->count(),
        ];

        $crops = Crop::select('id', 'name')->orderBy('name')->get();

        return view('yield_estimations.index', compact('query', 'stats', 'crops'));
    }

    public function create()
    {
        $crops = Crop::select('id', 'name', 'yield_unit', 'average_yield_per_hectare')->orderBy('name')->get();
        $farmers = Farmer::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $fields = Field::select('id', 'name')->orderBy('name')->get();

        return view('yield_estimations.create', compact('crops', 'farmers', 'fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'hectares' => 'nullable|numeric|min:0.01',
            'season' => 'required|string|max:50',
            'year' => 'required|integer|min:2000|max:'.(date('Y') + 10),
            'estimated_yield' => 'nullable|numeric|min:0',
            'actual_yield' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:20',
            'estimated_income' => 'nullable|numeric|min:0',
            'actual_income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,harvested,failed',
        ]);

        // Get or create farmer for current user
        $farmer = Farmer::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'first_name' => Auth::user()->name ?? 'Unknown',
                'last_name' => '',
                'is_active' => true,
            ]
        );
        $validated['farmer_id'] = $farmer->id;

        if ($validated['estimated_yield'] && $validated['hectares']) {
            $validated['yield_per_hectare'] = $validated['estimated_yield'] / $validated['hectares'];
        }

        try {
            $yieldEstimation = YieldEstimation::create($validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create estimation: '.$e->getMessage());
        }

        return redirect()
            ->route('yield_estimations.show', $yieldEstimation)
            ->with('success', 'Yield estimation created successfully.');
    }

    public function show(YieldEstimation $yieldEstimation)
    {
        $this->authorizeAccess($yieldEstimation);

        $yieldEstimation->load(['crop', 'farmer', 'field']);

        return view('yield_estimations.show', compact('yieldEstimation'));
    }

    public function edit(YieldEstimation $yieldEstimation)
    {
        $this->authorizeAccess($yieldEstimation);

        $crops = Crop::select('id', 'name', 'yield_unit', 'average_yield_per_hectare')->orderBy('name')->get();
        $farmers = Farmer::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $fields = Field::select('id', 'name')->orderBy('name')->get();

        return view('yield_estimations.edit', compact('yieldEstimation', 'crops', 'farmers', 'fields'));
    }

    public function update(Request $request, YieldEstimation $yieldEstimation)
    {
        $this->authorizeAccess($yieldEstimation);

        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'hectares' => 'nullable|numeric|min:0.01',
            'season' => 'required|string|max:50',
            'year' => 'required|integer|min:2000|max:'.(date('Y') + 10),
            'estimated_yield' => 'nullable|numeric|min:0',
            'actual_yield' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:20',
            'estimated_income' => 'nullable|numeric|min:0',
            'actual_income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,harvested,failed',
        ]);

        if ($validated['estimated_yield'] && $validated['hectares']) {
            $validated['yield_per_hectare'] = $validated['estimated_yield'] / $validated['hectares'];
        }

        $yieldEstimation->update($validated);

        return redirect()
            ->route('yield_estimations.show', $yieldEstimation)
            ->with('success', 'Yield estimation updated successfully.');
    }

    public function destroy(YieldEstimation $yieldEstimation)
    {
        $this->authorizeAccess($yieldEstimation);

        $yieldEstimation->delete();

        return redirect()
            ->route('yield-estimations.index')
            ->with('success', 'Yield estimation deleted successfully.');
    }

    public function dashboard()
    {
        $year = request('year', now()->year);

        $query = YieldEstimation::with(['crop', 'farmer', 'field']);

        if (! Auth::user()->isAdmin()) {
            $query->whereHas('farmer', fn ($q) => $q->where('user_id', Auth::id()));
        }

        $query->where('year', $year);

        $estimations = $query->latest()->get();

        $monthlyCounts = YieldEstimation::query()
            ->when(! Auth::user()->isAdmin(), fn ($q) => $q->whereHas('farmer', fn ($q) => $q->where('user_id', Auth::id())))
            ->where('year', $year)
            ->selectRaw('MONTH(created_at) as month_num, count(*) as count')
            ->groupBy('month_num')
            ->pluck('count', 'month_num');

        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $monthlyEstimations = collect();
        foreach ($monthNames as $num => $name) {
            $count = $monthlyCounts->get($num, 0);
            $monthlyEstimations->put($name, collect(array_fill(0, $count, ['id' => 1])));
        }

        $stats = [
            'total_estimations' => $estimations->count(),
            'total_planned_hectares' => $estimations->sum('hectares'),
            'estimated_total_yield' => $estimations->sum('estimated_yield'),
            'total_in_progress' => $estimations->where('status', 'in_progress')->count(),
            'total_harvested' => $estimations->where('status', 'harvested')->count(),
            'total_failed' => $estimations->where('status', 'failed')->count(),
        ];

        return view('yield_estimations.dashboard', compact(
            'estimations',
            'stats',
            'year',
            'monthlyEstimations'
        ));
    }

    public function statistics()
    {
        $startDate = request('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $baseQuery = YieldEstimation::query()
            ->when(! Auth::user()->isAdmin(), fn ($q) => $q->whereHas('farmer', fn ($q) => $q->where('user_id', Auth::id())));

        $stats = [
            'total_estimations' => (clone $baseQuery)->count(),
            'total_hectares' => (clone $baseQuery)->sum('hectares'),
            'estimated_total_yield' => (clone $baseQuery)->sum('estimated_yield'),
            'actual_total_yield' => (clone $baseQuery)->sum('actual_yield'),
            'total_income' => (clone $baseQuery)->sum('estimated_income'),
            'total_actual_income' => (clone $baseQuery)->sum('actual_income'),
        ];

        $byStatus = (clone $baseQuery)
            ->selectRaw('status as name, count(*) as count, sum(hectares) as hectares')
            ->groupBy('status')
            ->get();

        $byCrop = (clone $baseQuery)
            ->join('crops', 'yield_estimations.crop_id', '=', 'crops.id')
            ->selectRaw('crops.name, sum(estimated_yield) as yield, count(*) as count')
            ->groupBy('crops.id', 'crops.name')
            ->orderBy('yield', 'desc')
            ->get();

        $byMonth = (clone $baseQuery)
            ->selectRaw('MONTHNAME(created_at) as month, sum(estimated_yield) as yield, count(*) as count')
            ->groupBy('month')
            ->orderByRaw('MIN(month(created_at))')
            ->get();

        return view('yield_estimations.statistics', compact(
            'stats',
            'byStatus',
            'byCrop',
            'byMonth',
            'startDate',
            'endDate'
        ));
    }

    private function authorizeAccess(YieldEstimation $estimation): void
    {
        if (! Auth::user()->isAdmin() && $estimation->farmer && $estimation->farmer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
