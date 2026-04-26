<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Field;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request): View
    {
        $query = Task::with(['field', 'crop', 'assignedUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        }

        $tasks = $query->orderBy('scheduled_date', 'asc')->paginate(15);
        
        $fields = Field::all();
        $crops = Crop::all();
        $users = User::all();

        return view('tasks.index', compact('tasks', 'fields', 'crops', 'users'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        $fields = Field::all();
        $crops = Crop::all();
        $users = User::all();
        
        $taskTypes = [
            Task::TYPE_PLANTING => 'Planting',
            Task::TYPE_HARVESTING => 'Harvesting',
            Task::TYPE_IRRIGATION => 'Irrigation',
            Task::TYPE_FERTILIZING => 'Fertilizing',
            Task::TYPE_PEST_CONTROL => 'Pest Control',
            Task::TYPE_WEEDING => 'Weeding',
            Task::TYPE_SOIL_PREPARATION => 'Soil Preparation',
            Task::TYPE_MAINTENANCE => 'Maintenance',
            Task::TYPE_INSPECTION => 'Inspection',
            Task::TYPE_OTHER => 'Other',
        ];

        $priorities = [
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_NORMAL => 'Normal',
            Task::PRIORITY_HIGH => 'High',
            Task::PRIORITY_URGENT => 'Urgent',
        ];

        $statuses = [
            Task::STATUS_PENDING => 'Pending',
            Task::STATUS_IN_PROGRESS => 'In Progress',
            Task::STATUS_COMPLETED => 'Completed',
            Task::STATUS_CANCELLED => 'Cancelled',
            Task::STATUS_ON_HOLD => 'On Hold',
        ];

        return view('tasks.create', compact('fields', 'crops', 'users', 'taskTypes', 'priorities', 'statuses'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'nullable|exists:fields,id',
            'crop_id' => 'nullable|exists:crops,id',
            'task_type' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'scheduled_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): View
    {
        $task->load(['field', 'crop', 'assignedUser']);
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task): View
    {
        $fields = Field::all();
        $crops = Crop::all();
        $users = User::all();
        
        $taskTypes = [
            Task::TYPE_PLANTING => 'Planting',
            Task::TYPE_HARVESTING => 'Harvesting',
            Task::TYPE_IRRIGATION => 'Irrigation',
            Task::TYPE_FERTILIZING => 'Fertilizing',
            Task::TYPE_PEST_CONTROL => 'Pest Control',
            Task::TYPE_WEEDING => 'Weeding',
            Task::TYPE_SOIL_PREPARATION => 'Soil Preparation',
            Task::TYPE_MAINTENANCE => 'Maintenance',
            Task::TYPE_INSPECTION => 'Inspection',
            Task::TYPE_OTHER => 'Other',
        ];

        $priorities = [
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_NORMAL => 'Normal',
            Task::PRIORITY_HIGH => 'High',
            Task::PRIORITY_URGENT => 'Urgent',
        ];

        $statuses = [
            Task::STATUS_PENDING => 'Pending',
            Task::STATUS_IN_PROGRESS => 'In Progress',
            Task::STATUS_COMPLETED => 'Completed',
            Task::STATUS_CANCELLED => 'Cancelled',
            Task::STATUS_ON_HOLD => 'On Hold',
        ];

        return view('tasks.edit', compact('task', 'fields', 'crops', 'users', 'taskTypes', 'priorities', 'statuses'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'nullable|exists:fields,id',
            'crop_id' => 'nullable|exists:crops,id',
            'task_type' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Auto-set completed date if status is completed
        if ($validated['status'] === Task::STATUS_COMPLETED && !$validated['completed_date']) {
            $validated['completed_date'] = now();
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Mark task as completed.
     */
    public function complete(Task $task): RedirectResponse
    {
        $task->update([
            'status' => Task::STATUS_COMPLETED,
            'completed_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Task marked as completed.');
    }

    /**
     * Get upcoming tasks for dashboard.
     */
    public function getUpcomingTasks($limit = 5)
    {
        return Task::with(['field'])
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->where('status', '!=', Task::STATUS_CANCELLED)
            ->orderBy('scheduled_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue tasks.
     */
    public function getOverdueTasks()
    {
        return Task::with(['field'])
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->where('status', '!=', Task::STATUS_CANCELLED)
            ->where('scheduled_date', '<', now())
            ->orderBy('scheduled_date', 'asc')
            ->get();
    }
}