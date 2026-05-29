<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\LivestockDewormingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockDewormingScheduleController extends Controller
{
    public function index()
    {
        $schedules = LivestockDewormingSchedule::with(['livestock', 'livestockType'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('livestock_deworming_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $livestocks = Livestock::where('user_id', Auth::id())->whereIn('status', ['healthy', 'sick'])->get();
        $livestockTypes = LivestockType::all();

        return view('livestock_deworming_schedules.create', compact('livestocks', 'livestockTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'nullable|exists:livestock,id',
            'livestock_type_id' => 'nullable|exists:livestock_types,id',
            'dewormer_name' => 'required|string|max:255',
            'dewormer_type' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
            'administered_by' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        LivestockDewormingSchedule::create($validated);

        return redirect()->route('livestock_deworming_schedules.index')
            ->with('success', 'Deworming schedule created successfully.');
    }

    public function show(LivestockDewormingSchedule $livestockDewormingSchedule)
    {
        $this->authorizeOwnership($livestockDewormingSchedule);
        return view('livestock_deworming_schedules.show', compact('livestockDewormingSchedule'));
    }

    public function edit(LivestockDewormingSchedule $livestockDewormingSchedule)
    {
        $this->authorizeOwnership($livestockDewormingSchedule);
        $livestocks = Livestock::where('user_id', Auth::id())->whereIn('status', ['healthy', 'sick'])->get();
        $livestockTypes = LivestockType::all();

        return view('livestock_deworming_schedules.edit', compact('livestockDewormingSchedule', 'livestocks', 'livestockTypes'));
    }

    public function update(Request $request, LivestockDewormingSchedule $livestockDewormingSchedule)
    {
        $this->authorizeOwnership($livestockDewormingSchedule);

        $validated = $request->validate([
            'livestock_id' => 'nullable|exists:livestock,id',
            'livestock_type_id' => 'nullable|exists:livestock_types,id',
            'dewormer_name' => 'required|string|max:255',
            'dewormer_type' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
            'administered_date' => 'nullable|date',
            'administered_by' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,administered,missed,cancelled',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
        ]);

        $livestockDewormingSchedule->update($validated);

        return redirect()->route('livestock_deworming_schedules.index')
            ->with('success', 'Deworming schedule updated successfully.');
    }

    public function destroy(LivestockDewormingSchedule $livestockDewormingSchedule)
    {
        $this->authorizeOwnership($livestockDewormingSchedule);
        $livestockDewormingSchedule->delete();

        return redirect()->route('livestock_deworming_schedules.index')
            ->with('success', 'Deworming schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}