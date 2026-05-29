<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\LivestockVaccinationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockVaccinationScheduleController extends Controller
{
    public function index()
    {
        $schedules = LivestockVaccinationSchedule::with(['livestock', 'livestockType'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('livestock_vaccination_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $livestocks = Livestock::where('user_id', Auth::id())->whereIn('status', ['healthy', 'sick'])->get();
        $livestockTypes = LivestockType::all();

        return view('livestock_vaccination_schedules.create', compact('livestocks', 'livestockTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'nullable|exists:livestock,id',
            'livestock_type_id' => 'nullable|exists:livestock_types,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'administered_by' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        LivestockVaccinationSchedule::create($validated);

        return redirect()->route('livestock_vaccination_schedules.index')
            ->with('success', 'Vaccination schedule created successfully.');
    }

    public function show(LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);
        return view('livestock_vaccination_schedules.show', compact('livestockVaccinationSchedule'));
    }

    public function edit(LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);
        $livestocks = Livestock::where('user_id', Auth::id())->whereIn('status', ['healthy', 'sick'])->get();
        $livestockTypes = LivestockType::all();

        return view('livestock_vaccination_schedules.edit', compact('livestockVaccinationSchedule', 'livestocks', 'livestockTypes'));
    }

    public function update(Request $request, LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);

        $validated = $request->validate([
            'livestock_id' => 'nullable|exists:livestock,id',
            'livestock_type_id' => 'nullable|exists:livestock_types,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'administered_date' => 'nullable|date',
            'administered_by' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,administered,missed,cancelled',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
        ]);

        $livestockVaccinationSchedule->update($validated);

        return redirect()->route('livestock_vaccination_schedules.index')
            ->with('success', 'Vaccination schedule updated successfully.');
    }

    public function destroy(LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);
        $livestockVaccinationSchedule->delete();

        return redirect()->route('livestock_vaccination_schedules.index')
            ->with('success', 'Vaccination schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}