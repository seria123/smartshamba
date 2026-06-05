<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffAnalyticsController extends Controller
{
    public function index(): View
    {
        $farmId = request('farm_id');
        $query = Staff::with(['farm', 'performanceReviews']);

        if ($farmId) {
            $query->where('farm_id', $farmId);
        }

        $staff = $query->where('status', 'active')->get();

        $stats = [
            'total_staff' => $staff->count(),
            'active_today' => 0,
            'field_assignments' => 0,
            'tasks_pending' => 0,
            'avg_performance' => 0,
        ];

        $totalScore = 0;
        $scoredCount = 0;

        foreach ($staff as $member) {
            if ($member->activeLocation) {
                $stats['active_today']++;
            }
            $stats['field_assignments'] += $member->activeFieldAssignments->count();
            $stats['tasks_pending'] += $member->tasks_count;

            if ($member->performance_score) {
                $totalScore += $member->performance_score;
                $scoredCount++;
            }
        }

        if ($scoredCount > 0) {
            $stats['avg_performance'] = round($totalScore / $scoredCount, 2);
        }

        $farms = Farm::all();

        return view('staff.analytics.index', compact('staff', 'stats', 'farms', 'farmId'));
    }

    public function show(Staff $staff): View
    {
        $staff->load([
            'attendances' => function ($query) {
                $query->orderBy('date', 'desc')->limit(30);
            },
            'performanceReviews',
            'tasks',
            'activityLogs',
        ]);

        return view('staff.analytics.show', compact('staff'));
    }
}
