<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Farm;
use App\Models\Staff;
use App\Models\StaffFieldAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffFieldAssignmentController extends Controller
{
    public function index(Staff $staff): View
    {
        $staff->load(['fieldAssignments.field', 'fieldAssignments.farm']);
        $fields = Field::where('farm_id', $staff->farm_id)->get();
        $farms = Farm::all();

        return view('staff.field_assignments.index', compact('staff', 'fields', 'farms'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'farm_id' => 'required|exists:farms,id',
            'is_primary' => 'boolean',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        StaffFieldAssignment::create([
            'staff_id' => $staff->id,
            'field_id' => $validated['field_id'],
            'farm_id' => $validated['farm_id'],
            'is_primary' => $validated['is_primary'] ?? false,
            'assigned_date' => $validated['assigned_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Field assigned successfully.');
    }

    public function destroy(Staff $staff, StaffFieldAssignment $assignment): RedirectResponse
    {
        $assignment->update([
            'unassigned_date' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Field unassigned successfully.');
    }
}
