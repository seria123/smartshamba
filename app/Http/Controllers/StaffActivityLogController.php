<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Staff;
use App\Models\StaffActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffActivityLogController extends Controller
{
    public function index(Staff $staff): View
    {
        $logs = $staff->activityLogs()
            ->with(['field', 'task'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('staff.activity_logs.index', compact('staff', 'logs'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'field_id' => 'nullable|exists:fields,id',
            'task_id' => 'nullable|exists:tasks,id',
            'activity_type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'started_at' => 'nullable|date',
        ]);

        StaffActivityLog::create([
            'staff_id' => $staff->id,
            'farm_id' => $staff->farm_id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', 'Activity log created successfully.');
    }

    public function update(Request $request, Staff $staff, StaffActivityLog $log): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'ended_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        $log->update($validated);

        return redirect()->back()->with('success', 'Activity log updated successfully.');
    }
}
