<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffWage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Display a listing of staff.
     */
    public function index(Request $request): View
    {
        $query = Staff::with(['farm']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        $staff = $query->orderBy('first_name', 'asc')->paginate(15);
        $farms = Farm::all();

        return view('staff.index', compact('staff', 'farms'));
    }

    /**
     * Show the form for creating a new staff.
     */
    public function create(): View
    {
        $farms = Farm::all();

        $roles = [
            Staff::ROLE_GENERAL_WORKER => 'General Worker',
            Staff::ROLE_SUPERVISOR => 'Supervisor',
            Staff::ROLE_TECHNICIAN => 'Technician',
            Staff::ROLE_DRIVER => 'Driver',
            Staff::ROLE_HARVESTER => 'Harvester',
            Staff::ROLE_PLANTING => 'Planting',
            Staff::ROLE_IRRIGATION => 'Irrigation',
        ];

        $paymentTypes = [
            Staff::PAYMENT_DAILY => 'Daily',
            Staff::PAYMENT_WEEKLY => 'Weekly',
            Staff::PAYMENT_MONTHLY => 'Monthly',
            Staff::PAYMENT_PIECE_RATE => 'Piece Rate',
        ];

        $statuses = [
            Staff::STATUS_ACTIVE => 'Active',
            Staff::STATUS_INACTIVE => 'Inactive',
            Staff::STATUS_TERMINATED => 'Terminated',
        ];

        return view('staff.create', compact('farms', 'roles', 'paymentTypes', 'statuses'));
    }

    /**
     * Store a newly created staff in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|string|max:255',
            'national_id' => 'required|string|max:100|unique:staff,national_id',
            'employee_id' => 'nullable|string|max:100|unique:staff,employee_id',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'role' => 'required|string',
            'employment_type' => 'required|string|in:permanent,casual,seasonal',
            'daily_wage' => 'required|numeric|min:0',
            'payment_type' => 'required|string',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Staff::create($validated);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Display the specified staff.
     */
    public function show(Staff $staff): View
    {
        $staff->load([
            'farm',
            'attendances' => function ($query) {
                $query->orderBy('date', 'desc')->limit(10);
            },
            'wages' => function ($query) {
                $query->orderBy('payment_date', 'desc')->limit(10);
            },
            'tasks' => function ($query) {
                $query->orderBy('scheduled_date', 'desc')->limit(10);
            },
        ]);

        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified staff.
     */
    public function edit(Staff $staff): View
    {
        $farms = Farm::all();

        $roles = [
            Staff::ROLE_GENERAL_WORKER => 'General Worker',
            Staff::ROLE_SUPERVISOR => 'Supervisor',
            Staff::ROLE_TECHNICIAN => 'Technician',
            Staff::ROLE_DRIVER => 'Driver',
            Staff::ROLE_HARVESTER => 'Harvester',
            Staff::ROLE_PLANTING => 'Planting',
            Staff::ROLE_IRRIGATION => 'Irrigation',
        ];

        $paymentTypes = [
            Staff::PAYMENT_DAILY => 'Daily',
            Staff::PAYMENT_WEEKLY => 'Weekly',
            Staff::PAYMENT_MONTHLY => 'Monthly',
            Staff::PAYMENT_PIECE_RATE => 'Piece Rate',
        ];

        $statuses = [
            Staff::STATUS_ACTIVE => 'Active',
            Staff::STATUS_INACTIVE => 'Inactive',
            Staff::STATUS_TERMINATED => 'Terminated',
        ];

        return view('staff.edit', compact('staff', 'farms', 'roles', 'paymentTypes', 'statuses'));
    }

    /**
     * Update the specified staff in storage.
     */
    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|string|max:255',
            'national_id' => 'required|string|max:100|unique:staff,national_id,'.$staff->id,
            'employee_id' => 'nullable|string|max:100|unique:staff,employee_id,'.$staff->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'role' => 'required|string',
            'employment_type' => 'required|string|in:permanent,casual,seasonal',
            'daily_wage' => 'required|numeric|min:0',
            'payment_type' => 'required|string',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.show', $staff)
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified staff from storage.
     */
    public function destroy(Staff $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Show staff attendance management page.
     */
    public function attendance(Staff $staff): View
    {
        $staff->load(['farm', 'attendances' => function ($query) {
            $query->orderBy('date', 'desc')->paginate(30);
        }]);

        return view('staff.attendance', compact('staff'));
    }

    /**
     * Store attendance record for a staff.
     */
    public function storeAttendance(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'required|string',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string',
        ]);

        $attendance = StaffAttendance::firstOrCreate(
            [
                'staff_id' => $staff->id,
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'hours_worked' => $validated['hours_worked'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Attendance recorded successfully.');
    }

    /**
     * Show staff wages page.
     */
    public function wages(Staff $staff): View
    {
        $staff->load(['farm', 'wages' => function ($query) {
            $query->orderBy('payment_date', 'desc')->paginate(20);
        }]);

        return view('staff.wages', compact('staff'));
    }

    /**
     * Store wage payment for a staff.
     */
    public function storeWage(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        StaffWage::create([
            'staff_id' => $staff->id,
            'farm_id' => $staff->farm_id,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Wage payment recorded successfully.');
    }

    /**
     * Update wage payment for a staff.
     */
    public function updateWage(Request $request, StaffWage $wage): RedirectResponse
    {
        $this->authorize('update', $wage);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $wage->update($validated);

        return redirect()->route('staff.wages', $wage->staff)
            ->with('success', 'Wage payment updated successfully.');
    }

    /**
     * Show staff tasks page.
     */
    public function tasks(Staff $staff): View
    {
        $staff->load([
            'farm',
            'tasks' => function ($query) {
                $query->orderBy('scheduled_date', 'desc')->paginate(20);
            },
        ]);

        return view('staff.tasks', compact('staff'));
    }

    /**
     * Terminate a staff member (admin only).
     */
    public function terminate(Staff $staff): RedirectResponse
    {
        $staff->update([
            'status' => Staff::STATUS_TERMINATED,
            'termination_date' => now(),
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member terminated successfully.');
    }
}
