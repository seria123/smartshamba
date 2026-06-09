<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Farm;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffScheduleController extends Controller
{
    public function all(): View
    {
        $staff = null;
        $schedules = StaffSchedule::with(['staff', 'field', 'farm'])
            ->orderBy('schedule_date', 'desc')
            ->paginate(20);
        $fields = Field::orderBy('name')->get();

        return view('staff.schedules.index', compact('staff', 'schedules', 'fields'));
    }

    public function index(Staff $staff): View
    {
        $schedules = $staff->schedules()
            ->with(['field', 'farm'])
            ->orderBy('schedule_date', 'desc')
            ->paginate(20);

        $fields = Field::where('farm_id', $staff->farm_id)->get();

        return view('staff.schedules.index', compact('staff', 'schedules', 'fields'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'field_id' => 'nullable|exists:fields,id',
            'farm_id' => 'required|exists:farms,id',
            'schedule_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'shift_type' => 'nullable|string|in:morning,afternoon,night,full_day',
            'notes' => 'nullable|string',
        ]);

        StaffSchedule::create([
            'staff_id' => $staff->id,
            ...$validated,
            'status' => 'scheduled',
        ]);

        return redirect()->back()->with('success', 'Schedule created successfully.');
    }

    public function update(Request $request, Staff $staff, StaffSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:scheduled,checked_in,checked_out,missed,cancelled',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $schedule->update($validated);

        return redirect()->back()->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Staff $staff, StaffSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->back()->with('success', 'Schedule deleted successfully.');
    }
}
