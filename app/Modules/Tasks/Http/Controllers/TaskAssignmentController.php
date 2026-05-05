<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tasks\Http\Controllers\Concerns\ValidatesTaskScope;
use App\Modules\Tasks\Models\OpsTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskAssignmentController extends Controller
{
    use ValidatesTaskScope;

    public function store(Request $request, OpsTask $task): RedirectResponse
    {
        $data = $request->validate([
            'assignment_type' => ['required', Rule::in(['worker', 'team', 'user'])],
            'worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'team_id' => ['nullable', 'integer', Rule::exists('labour_teams', 'id')],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'role_on_task' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        match ($data['assignment_type']) {
            'worker' => $this->ensureWorkerBelongsToFarm((int) ($data['worker_id'] ?? 0), (int) $task->farm_id),
            'team' => $this->ensureTeamBelongsToFarm((int) ($data['team_id'] ?? 0), (int) $task->farm_id),
            'user' => $this->ensureUserHasTaskFarmAccess((int) ($data['user_id'] ?? 0), (int) $task->organization_id, (int) $task->farm_id),
        };

        if ($data['assignment_type'] !== 'worker') {
            $data['worker_id'] = null;
        }
        if ($data['assignment_type'] !== 'team') {
            $data['team_id'] = null;
        }
        if ($data['assignment_type'] !== 'user') {
            $data['user_id'] = null;
        }

        $task->assignments()->create(array_merge($data, [
            'organization_id' => $task->organization_id,
            'farm_id' => $task->farm_id,
            'status' => 'assigned',
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
        ]));

        if ($task->status === 'draft') {
            $task->update(['status' => 'assigned', 'assigned_by' => $request->user()->id]);
            $task->updates()->create([
                'organization_id' => $task->organization_id,
                'farm_id' => $task->farm_id,
                'user_id' => $request->user()->id,
                'update_type' => 'status_change',
                'status_from' => 'draft',
                'status_to' => 'assigned',
                'notes' => 'Task assigned.',
            ]);
        }

        return redirect()->route('tasks.items.show', $task)->with('status', 'Task assigned.');
    }
}
