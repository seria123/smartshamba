<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Farm;
use App\Models\Staff;
use App\Models\StaffLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffLocationController extends Controller
{
    public function index(Staff $staff): View
    {
        $locations = $staff->locations()
            ->with('field')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $fields = $staff->activeFieldAssignments()->with('field')->get()->pluck('field');

        return view('staff.locations.index', compact('staff', 'locations', 'fields'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'field_id' => 'nullable|exists:fields,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_type' => 'required|string|max:50',
            'checkin_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $existingActive = $staff->locations()->whereNull('checked_out_at')->latest()->first();

        if ($existingActive && $request->input('action') === 'checkout') {
            $existingActive->update([
                'checked_out_at' => now(),
            ]);
        } elseif ($request->input('action') === 'checkin') {
            $validated['checked_in_at'] = now();
            StaffLocation::create($validated + ['staff_id' => $staff->id, 'farm_id' => $staff->farm_id]);
        }

        return redirect()->back()->with('success', 'Location updated successfully.');
    }

    public function fieldPresence(): View
    {
        $fieldId = request('field_id');
        $presence = collect();

        if ($fieldId) {
            $presence = StaffLocation::where('field_id', $fieldId)
                ->whereNotNull('checked_in_at')
                ->whereNull('checked_out_at')
                ->with(['staff', 'field'])
                ->get()
                ->unique('staff_id');
        }

        $fields = \App\Models\Field::all();

        return view('staff.locations.field_presence', compact('presence', 'fields', 'fieldId'));
    }
}
