<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsWorkOrder;
use Illuminate\Contracts\View\View;

class TaskDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('tasks::dashboard', [
            'counts' => [
                'Open tasks' => OpsTask::query()->whereNotIn('status', ['completed', 'cancelled', 'approved'])->count(),
                'Due today' => OpsTask::query()->whereDate('due_date', today())->count(),
                'Overdue' => OpsTask::query()->whereNotIn('status', ['completed', 'cancelled', 'approved'])->whereDate('due_date', '<', today())->count(),
                'Pending approval' => OpsTask::query()->where('status', 'submitted')->count(),
                'Work orders' => OpsWorkOrder::query()->where('status', '!=', 'cancelled')->count(),
            ],
        ]);
    }
}
