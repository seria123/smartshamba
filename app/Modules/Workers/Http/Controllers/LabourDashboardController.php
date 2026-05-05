<?php

namespace App\Modules\Workers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Workers\Models\LabourAttendanceRecord;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Contracts\View\View;

class LabourDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('labour::dashboard', [
            'counts' => [
                'Workers' => LabourWorker::count(),
                'Active workers' => LabourWorker::query()->where('status', 'active')->count(),
                'Teams' => LabourTeam::count(),
                'Attendance records' => LabourAttendanceRecord::count(),
            ],
        ]);
    }
}
