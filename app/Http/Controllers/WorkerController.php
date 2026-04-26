<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Worker;
use App\Models\WorkerAttendance;
use App\Models\WorkerWage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerController extends Controller
{
    /**
     * Display a listing of workers.
     */
    public function index(Request $request): View
    {
        $query = Worker::with(['farm']);

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

        $workers = $query->orderBy('first_name', 'asc')->paginate(15);
        $farms = Farm::all();

        return view('workers.index', compact('workers', 'farms'));
    }

    /**
     * Show the form for creating a new worker.
     */
    public function create(): View
    {
        $farms = Farm::all();

        $roles = [
            Worker::ROLE_GENERAL_WORKER => 'General Worker',
            Worker::ROLE_SUPERVISOR => 'Supervisor',
            Worker::ROLE_TECHNICIAN => 'Technician',
            Worker::ROLE_DRIVER => 'Driver',
            Worker::ROLE_HARVESTER => 'Harvester',
            Worker::ROLE_PLANTING => 'Planting',
            Worker::ROLE_IRRIGATION => 'Irrigation',
        ];

        $paymentTypes = [
            Worker::PAYMENT_DAILY => 'Daily',
            Worker::PAYMENT_WEEKLY => 'Weekly',
            Worker::PAYMENT_MONTHLY => 'Monthly',
            Worker::PAYMENT_PIECE_RATE => 'Piece Rate',
        ];

        $statuses = [
            Worker::STATUS_ACTIVE => 'Active',
            Worker::STATUS_INACTIVE => 'Inactive',
            Worker::STATUS_TERMINATED => 'Terminated',
        ];

        return view('workers.create', compact('farms', 'roles', 'paymentTypes', 'statuses'));
    }

    /**
     * Store a newly created worker in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'required|string|max:100|unique:workers,national_id',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'role' => 'required|string',
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

        Worker::create($validated);

        return redirect()->route('workers.index')
            ->with('success', 'Worker created successfully.');
    }

    /**
     * Display the specified worker.
     */
    public function show(Worker $worker): View
    {
        $worker->load([
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

        return view('workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified worker.
     */
    public function edit(Worker $worker): View
    {
        $farms = Farm::all();

        $roles = [
            Worker::ROLE_GENERAL_WORKER => 'General Worker',
            Worker::ROLE_SUPERVISOR => 'Supervisor',
            Worker::ROLE_TECHNICIAN => 'Technician',
            Worker::ROLE_DRIVER => 'Driver',
            Worker::ROLE_HARVESTER => 'Harvester',
            Worker::ROLE_PLANTING => 'Planting',
            Worker::ROLE_IRRIGATION => 'Irrigation',
        ];

        $paymentTypes = [
            Worker::PAYMENT_DAILY => 'Daily',
            Worker::PAYMENT_WEEKLY => 'Weekly',
            Worker::PAYMENT_MONTHLY => 'Monthly',
            Worker::PAYMENT_PIECE_RATE => 'Piece Rate',
        ];

        $statuses = [
            Worker::STATUS_ACTIVE => 'Active',
            Worker::STATUS_INACTIVE => 'Inactive',
            Worker::STATUS_TERMINATED => 'Terminated',
        ];

        return view('workers.edit', compact('worker', 'farms', 'roles', 'paymentTypes', 'statuses'));
    }

    /**
     * Update the specified worker in storage.
     */
    public function update(Request $request, Worker $worker): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'required|string|max:100|unique:workers,national_id,'.$worker->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'role' => 'required|string',
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

        $worker->update($validated);

        return redirect()->route('workers.show', $worker)
            ->with('success', 'Worker updated successfully.');
    }

    /**
     * Remove the specified worker from storage.
     */
    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();

        return redirect()->route('workers.index')
            ->with('success', 'Worker deleted successfully.');
    }

    /**
     * Show worker attendance management page.
     */
    public function attendance(Worker $worker): View
    {
        $worker->load(['farm', 'attendances' => function ($query) {
            $query->orderBy('date', 'desc')->paginate(30);
        }]);

        return view('workers.attendance', compact('worker'));
    }

    /**
     * Store attendance record for a worker.
     */
    public function storeAttendance(Request $request, Worker $worker): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'required|string',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string',
        ]);

        $attendance = WorkerAttendance::firstOrCreate(
            [
                'worker_id' => $worker->id,
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
     * Show worker wages page.
     */
    public function wages(Worker $worker): View
    {
        $worker->load(['farm', 'wages' => function ($query) {
            $query->orderBy('payment_date', 'desc')->paginate(20);
        }]);

        return view('workers.wages', compact('worker'));
    }

    /**
     * Store wage payment for a worker.
     */
    public function storeWage(Request $request, Worker $worker): RedirectResponse
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        WorkerWage::create([
            'worker_id' => $worker->id,
            'farm_id' => $worker->farm_id,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Wage payment recorded successfully.');
    }

    /**
     * Update wage payment for a worker.
     */
    public function updateWage(Request $request, WorkerWage $wage): RedirectResponse
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

        return redirect()->route('workers.wages', $wage->worker)
            ->with('success', 'Wage payment updated successfully.');
    }

    /**
     * Show worker tasks page.
     */
    public function tasks(Worker $worker): View
    {
        $worker->load([
            'farm',
            'tasks' => function ($query) {
                $query->orderBy('scheduled_date', 'desc')->paginate(20);
            },
        ]);

        return view('workers.tasks', compact('worker'));
    }

    /**
     * Terminate a worker (admin only).
     */
    public function terminate(Worker $worker): RedirectResponse
    {
        $worker->update([
            'status' => Worker::STATUS_TERMINATED,
            'termination_date' => now(),
        ]);

        return redirect()->route('workers.index')
            ->with('success', 'Worker terminated successfully.');
    }
}
