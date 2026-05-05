<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tasks\Models\OpsTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskUpdateController extends Controller
{
    public function store(Request $request, OpsTask $task): RedirectResponse
    {
        $data = $request->validate([
            'update_type' => ['required', Rule::in(['comment', 'progress', 'issue', 'status_change', 'submission', 'approval', 'rejection', 'correction_request', 'completion'])],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'quantity_done' => ['nullable', 'numeric', 'min:0'],
            'quantity_unit' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $task->updates()->create(array_merge($data, [
            'organization_id' => $task->organization_id,
            'farm_id' => $task->farm_id,
            'user_id' => $request->user()->id,
        ]));

        return redirect()->route('tasks.items.show', $task)->with('status', 'Task update recorded.');
    }
}
