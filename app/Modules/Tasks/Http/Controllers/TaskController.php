<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Tasks\Http\Controllers\Concerns\ValidatesTaskScope;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsTaskUpdate;
use App\Modules\Tasks\Models\OpsWorkOrder;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    use ValidatesTaskScope;

    public function index(): View
    {
        return view('tasks::items.index', [
            'tasks' => OpsTask::query()
                ->with(['farm', 'workOrder', 'assignments.worker', 'assignments.team', 'assignments.user'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('tasks::items.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['task_number'] = $this->nextNumber('TASK', 'ops_tasks', 'task_number');
        $data['created_by'] = $request->user()->id;

        $task = OpsTask::query()->create($data);
        $this->logStatus($task, null, $task->status, $request->user()->id, 'Task created.');

        return redirect()->route('tasks.items.show', $task)->with('status', 'Task created.');
    }

    public function show(OpsTask $task): View
    {
        return view('tasks::items.show', [
            'task' => $task->load([
                'organization',
                'farm',
                'workOrder',
                'site',
                'field',
                'paddock',
                'warehouse',
                'assignments.worker',
                'assignments.team',
                'assignments.user',
                'updates.user',
                'checklistItems',
            ]),
            'workers' => LabourWorker::query()->where('farm_id', $task->farm_id)->orderBy('name')->get(),
            'teams' => LabourTeam::query()->where('farm_id', $task->farm_id)->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(OpsTask $task): View
    {
        return view('tasks::items.form', $this->formData($task));
    }

    public function update(Request $request, OpsTask $task): RedirectResponse
    {
        $oldStatus = $task->status;
        $data = $this->validated($request);
        $data['updated_by'] = $request->user()->id;
        $task->update($data);

        if ($oldStatus !== $task->status) {
            $this->logStatus($task, $oldStatus, $task->status, $request->user()->id, 'Task status updated.');
        }

        return redirect()->route('tasks.items.show', $task)->with('status', 'Task updated.');
    }

    public function start(Request $request, OpsTask $task): RedirectResponse
    {
        return $this->changeStatus($request, $task, 'in_progress', ['started_at' => now()], 'Task started.');
    }

    public function submit(Request $request, OpsTask $task): RedirectResponse
    {
        return $this->changeStatus($request, $task, 'submitted', ['submitted_at' => now()], 'Task submitted.');
    }

    public function approve(Request $request, OpsTask $task): RedirectResponse
    {
        $data = $request->validate(['approval_notes' => ['nullable', 'string', 'max:2000']]);

        return $this->changeStatus($request, $task, 'completed', [
            'approved_at' => now(),
            'completed_at' => now(),
            'approved_by' => $request->user()->id,
            'approval_notes' => $data['approval_notes'] ?? null,
        ], 'Task approved.');
    }

    public function reject(Request $request, OpsTask $task): RedirectResponse
    {
        $data = $request->validate(['approval_notes' => ['nullable', 'string', 'max:2000']]);

        return $this->changeStatus($request, $task, 'needs_correction', [
            'rejected_at' => now(),
            'approval_notes' => $data['approval_notes'] ?? null,
        ], 'Task needs correction.');
    }

    public function cancel(Request $request, OpsTask $task): RedirectResponse
    {
        $data = $request->validate(['cancellation_reason' => ['nullable', 'string', 'max:2000']]);

        return $this->changeStatus($request, $task, 'cancelled', [
            'cancelled_at' => now(),
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
        ], 'Task cancelled.');
    }

    private function formData(?OpsTask $task = null): array
    {
        return [
            'task' => $task,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'workOrders' => OpsWorkOrder::query()->with('farm')->orderByDesc('id')->get(),
            'sites' => Site::query()->with('farm')->orderBy('name')->get(),
            'fields' => Field::query()->with('farm')->orderBy('name')->get(),
            'paddocks' => Paddock::query()->with('farm')->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->with('farm')->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'work_order_id' => ['nullable', 'integer', Rule::exists('ops_work_orders', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', Rule::in(['general', 'crop', 'livestock', 'irrigation', 'inventory', 'maintenance', 'harvest', 'feeding', 'treatment', 'cleaning', 'security', 'other'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['draft', 'assigned', 'in_progress', 'submitted', 'needs_correction', 'approved', 'completed', 'cancelled', 'rejected'])],
            'site_id' => ['nullable', 'integer', Rule::exists('sites', 'id')],
            'field_id' => ['nullable', 'integer', Rule::exists('fields', 'id')],
            'paddock_id' => ['nullable', 'integer', Rule::exists('paddocks', 'id')],
            'warehouse_id' => ['nullable', 'integer', Rule::exists('warehouses', 'id')],
            'supervisor_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        $this->ensureWorkOrderBelongsToFarm($data['work_order_id'] ?? null, (int) $data['farm_id']);
        $this->ensureLocationsBelongToFarm($data);

        if ($data['supervisor_user_id'] ?? null) {
            $this->ensureUserHasOrganizationAccess((int) $data['supervisor_user_id'], (int) $data['organization_id']);
        }

        return $data;
    }

    private function changeStatus(Request $request, OpsTask $task, string $status, array $extra, string $message): RedirectResponse
    {
        $oldStatus = $task->status;
        $task->update(array_merge(['status' => $status, 'updated_by' => $request->user()->id], $extra));
        $this->logStatus($task, $oldStatus, $status, $request->user()->id, $message);

        return redirect()->route('tasks.items.show', $task)->with('status', $message);
    }

    private function logStatus(OpsTask $task, ?string $from, string $to, int $userId, string $notes): void
    {
        OpsTaskUpdate::query()->create([
            'organization_id' => $task->organization_id,
            'farm_id' => $task->farm_id,
            'task_id' => $task->id,
            'user_id' => $userId,
            'update_type' => 'status_change',
            'status_from' => $from,
            'status_to' => $to,
            'notes' => $notes,
        ]);
    }
}
