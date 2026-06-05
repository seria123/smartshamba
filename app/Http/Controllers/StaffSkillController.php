<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffSkill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffSkillController extends Controller
{
    public function index(Staff $staff): View
    {
        $staff->load('skills');
        $skillCategories = [
            'field_operations' => 'Field Operations',
            'equipment' => 'Equipment Operation',
            'crop_management' => 'Crop Management',
            'livestock' => 'Livestock',
            'maintenance' => 'Maintenance',
            'other' => 'Other',
        ];

        return view('staff.skills.index', compact('staff', 'skillCategories'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'skill_category' => 'nullable|string|max:100',
            'proficiency_level' => 'required|string|in:beginner,intermediate,advanced,expert',
            'certification' => 'nullable|string|max:255',
            'certification_expiry' => 'nullable|date',
            'acquired_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        StaffSkill::create([
            'staff_id' => $staff->id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', 'Skill added successfully.');
    }

    public function destroy(Staff $staff, StaffSkill $skill): RedirectResponse
    {
        $skill->delete();

        return redirect()->back()->with('success', 'Skill removed successfully.');
    }
}
