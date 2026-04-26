<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Field;
use App\Models\Harvest;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    public function index(Request $request)
    {
        $query = Harvest::with(['crop', 'field', 'farm']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('harvest_batch', 'like', "%{$search}%")
                    ->orWhereHas('crop', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('crop_id')) {
            $query->where('crop_id', $request->crop_id);
        }

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        if ($request->filled('quality_grade')) {
            $query->where('quality_grade', $request->quality_grade);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('harvest_date', [$request->start_date, $request->end_date]);
        }

        $harvests = $query->orderBy('harvest_date', 'desc')->paginate(15);

        $stats = [
            'total_harvested' => (float) $query->sum('quantity_harvested'),
            'total_losses' => (float) $query->sum('loss_quantity'),
            'grade_a_count' => (int) $query->where('quality_grade', Harvest::GRADE_A)->count(),
            'grade_b_count' => (int) $query->where('quality_grade', Harvest::GRADE_B)->count(),
            'grade_c_count' => (int) $query->where('quality_grade', Harvest::GRADE_C)->count(),
        ];

        return view('harvests.index', compact('harvests', 'stats'));
    }

    public function show(Harvest $harvest)
    {
        $harvest->load(['crop', 'field', 'farm']);

        return view('harvests.show', compact('harvest'));
    }

    public function create()
    {
        $crops = Crop::all();
        $fields = Field::all();

        return view('harvests.create', compact('crops', 'fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'required|exists:fields,id',
            'farm_id' => 'required|exists:farms,id',
            'harvest_date' => 'required|date',
            'harvest_batch' => 'nullable|string|max:100',
            'quantity_harvested' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:20',
            'quality_grade' => 'required|in:grade_a,grade_b,grade_c,reject',
            'quality_percentage' => 'nullable|numeric|min:0|max:100',
            'loss_quantity' => 'nullable|numeric|min:0',
            'loss_reason' => 'nullable|string|max:255',
            'storage_location' => 'required|in:field,barn,warehouse,cold_storage,sold_immediately',
            'storage_details' => 'nullable|string',
            'moisture_content' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $harvest = Harvest::create($validated);

        if ($harvest->loss_quantity > 0) {
            $harvest->calculateLossPercentage();
            $harvest->save();
        }

        return redirect()->route('harvests.index')
            ->with('success', 'Harvest record created successfully');
    }

    public function edit(Harvest $harvest)
    {
        $crops = Crop::all();
        $fields = Field::all();

        return view('harvests.edit', compact('harvest', 'crops', 'fields'));
    }

    public function update(Request $request, Harvest $harvest)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'required|exists:fields,id',
            'farm_id' => 'required|exists:farms,id',
            'harvest_date' => 'required|date',
            'harvest_batch' => 'nullable|string|max:100',
            'quantity_harvested' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:20',
            'quality_grade' => 'required|in:grade_a,grade_b,grade_c,reject',
            'quality_percentage' => 'nullable|numeric|min:0|max:100',
            'loss_quantity' => 'nullable|numeric|min:0',
            'loss_reason' => 'nullable|string|max:255',
            'storage_location' => 'required|in:field,barn,warehouse,cold_storage,sold_immediately',
            'storage_details' => 'nullable|string',
            'moisture_content' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $harvest->update($validated);

        if ($harvest->loss_quantity > 0) {
            $harvest->calculateLossPercentage();
            $harvest->save();
        }

        return redirect()->route('harvests.show', $harvest)
            ->with('success', 'Harvest record updated successfully');
    }

    public function destroy(Harvest $harvest)
    {
        $harvest->delete();

        return redirect()->route('harvests.index')
            ->with('success', 'Harvest record deleted successfully');
    }

    public function dashboard(Request $request)
    {
        $year = $request->filled('year') ? $request->year : now()->year;
        $month = $request->filled('month') ? $request->month : now()->month;

        $harvests = Harvest::whereYear('harvest_date', $year)
            ->whereMonth('harvest_date', $month)
            ->with(['crop', 'field'])
            ->orderBy('harvest_date', 'desc')
            ->get();

        $stats = [
            'total_quantity' => $harvests->sum('quantity_harvested'),
            'total_losses' => $harvests->sum('loss_quantity'),
            'net_quantity' => $harvests->sum('quantity_harvested') - $harvests->sum('loss_quantity'),
            'grade_a_percentage' => $harvests->count() > 0
                ? ($harvests->where('quality_grade', Harvest::GRADE_A)->count() / $harvests->count()) * 100
                : 0,
            'by_crop' => $harvests->groupBy('crop_id')->map(function ($group) {
                $crop = $group->first()->crop;

                return [
                    'name' => $crop?->name ?? 'Unknown',
                    'quantity' => $group->sum('quantity_harvested'),
                    'losses' => $group->sum('loss_quantity'),
                ];
            }),
        ];

        return view('harvests.dashboard', compact('harvests', 'stats', 'year', 'month'));
    }

    public function statistics(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : now()->startOfYear()->toDateString();
        $endDate = $request->filled('end_date')
            ? $request->end_date
            : now()->endOfYear()->toDateString();

        $harvests = Harvest::whereBetween('harvest_date', [$startDate, $endDate])
            ->with(['crop', 'field']);

        $stats = [
            'total_harvests' => $harvests->count(),
            'total_quantity' => (float) $harvests->sum('quantity_harvested'),
            'total_losses' => (float) $harvests->sum('loss_quantity'),
            'average_quality' => (float) $harvests->avg('quality_percentage'),
            'by_crop' => $harvests->get()->groupBy('crop_id')->map(function ($group) {
                $crop = $group->first()->crop;

                return [
                    'name' => $crop?->name ?? 'Unknown',
                    'total_harvests' => $group->count(),
                    'total_quantity' => $group->sum('quantity_harvested'),
                    'total_losses' => $group->sum('loss_quantity'),
                ];
            })->values(),
            'by_field' => $harvests->get()->groupBy('field_id')->map(function ($group) {
                $field = $group->first()->field;

                return [
                    'name' => $field?->name ?? 'Unknown',
                    'total_harvests' => $group->count(),
                    'total_quantity' => $group->sum('quantity_harvested'),
                    'total_losses' => $group->sum('loss_quantity'),
                ];
            })->values(),
            'by_month' => $harvests->get()->groupBy(function ($h) {
                return $h->harvest_date->format('Y-m');
            })->map(function ($group) {
                return [
                    'month' => $group->first()->harvest_date->format('F Y'),
                    'total_quantity' => $group->sum('quantity_harvested'),
                    'total_losses' => $group->sum('loss_quantity'),
                ];
            })->values(),
        ];

        return view('harvests.statistics', compact('harvests', 'stats', 'startDate', 'endDate'));
    }
}
