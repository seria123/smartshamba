<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsTaskChecklistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskChecklistController extends Controller
{
    public function store(Request $request, OpsTask $task): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $task->checklistItems()->create([
            'label' => $data['label'],
            'is_required' => (bool) ($data['is_required'] ?? false),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('tasks.items.show', $task)->with('status', 'Checklist item added.');
    }

    public function toggle(Request $request, OpsTaskChecklistItem $item): RedirectResponse
    {
        $isCompleted = ! $item->is_completed;

        $item->update([
            'is_completed' => $isCompleted,
            'completed_by' => $isCompleted ? $request->user()->id : null,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        return redirect()->route('tasks.items.show', $item->task)->with('status', 'Checklist updated.');
    }
}
