<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\LivestockFumigationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockFumigationScheduleController extends Controller
{
    public function index()
    {
        $schedules = LivestockFumigationSchedule::with('farm')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('livestock_fumigation_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $farms = Farm::where('user_id', Auth::id())->get();

        return view('livestock_fumigation_schedules.create', compact('farms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'nullable|exists:farms,id',
            'fumigant_name' => 'required|string|max:255',
            'fumigation_type' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'area_covered' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'performed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        LivestockFumigationSchedule::create($validated);

        return redirect()->route('livestock_fumigation_schedules.index')
            ->with('success', 'Fumigation schedule created successfully.');
    }

    public function show(LivestockFumigationSchedule $livestockFumigationSchedule)
    {
        $this->authorizeOwnership($livestockFumigationSchedule);
        return view('livestock_fumigation_schedules.show', compact('livestockFumigationSchedule'));
    }

    public function edit(LivestockFumigationSchedule $livestockFumigationSchedule)
    {
        $this->authorizeOwnership($livestockFumigationSchedule);
        $farms = Farm::where('user_id', Auth::id())->get();

        return view('livestock_fumigation_schedules.edit', compact('livestockFumigationSchedule', 'farms'));
    }

    public function update(Request $request, LivestockFumigationSchedule $livestockFumigationSchedule)
    {
        $this->authorizeOwnership($livestockFumigationSchedule);

        $validated = $request->validate([
            'farm_id' => 'nullable|exists:farms,id',
            'fumigant_name' => 'required|string|max:255',
            'fumigation_type' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'area_covered' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'performed_date' => 'nullable|date',
            'performed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,missed,cancelled',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
        ]);

        $livestockFumigationSchedule->update($validated);

        return redirect()->route('livestock_fumigation_schedules.index')
            ->with('success', 'Fumigation schedule updated successfully.');
    }

    public function destroy(LivestockFumigationSchedule $livestockFumigationSchedule)
    {
        $this->authorizeOwnership($livestockFumigationSchedule);
        $livestockFumigationSchedule->delete();

        return redirect()->route('livestock_fumigation_schedules.index')
            ->with('success', 'Fumigation schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}