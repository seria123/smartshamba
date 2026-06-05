<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Staff;
use App\Models\StaffPerformanceReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffPerformanceReviewController extends Controller
{
    public function index(Staff $staff): View
    {
        $reviews = $staff->performanceReviews()
            ->with('reviewer')
            ->orderBy('review_period_start', 'desc')
            ->paginate(20);

        return view('staff.performance.index', compact('staff', 'reviews'));
    }

    public function create(Staff $staff): View
    {
        $farms = Farm::all();
        $reviewers = Staff::where('farm_id', $staff->farm_id)
            ->where('id', '!=', $staff->id)
            ->get();

        return view('staff.performance.create', compact('staff', 'farms', 'reviewers'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'reviewed_by' => 'nullable|exists:staff,id',
            'farm_id' => 'required|exists:farms,id',
            'review_period_start' => 'required|date',
            'review_period_end' => 'required|date|after:review_period_start',
            'tasks_completed' => 'required|integer|min:0',
            'tasks_assigned' => 'required|integer|min:0',
            'attendance_rate' => 'required|numeric|min:0|max:100',
            'work_quality_score' => 'nullable|numeric|min:0|max:100',
            'efficiency_rating' => 'nullable|numeric|min:0|max:100',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $tasksCompleted = $validated['tasks_completed'];
        $tasksAssigned = $validated['tasks_assigned'];
        $attendanceRate = $validated['attendance_rate'];
        $workQuality = $validated['work_quality_score'] ?? 0;
        $efficiency = $validated['efficiency_rating'] ?? 0;

        $overallScore = ($tasksAssigned > 0 ? ($tasksCompleted / $tasksAssigned) * 25 : 0)
            + ($attendanceRate * 0.25)
            + ($workQuality * 0.25)
            + ($efficiency * 0.25);

        $rating = match (true) {
            $overallScore >= 90 => 'excellent',
            $overallScore >= 75 => 'good',
            $overallScore >= 60 => 'average',
            $overallScore >= 40 => 'poor',
            default => 'very_poor',
        };

        StaffPerformanceReview::create([
            'staff_id' => $staff->id,
            ...$validated,
            'overall_score' => round($overallScore, 2),
            'rating' => $rating,
        ]);

        return redirect()->route('staff.performance.index', $staff)
            ->with('success', 'Performance review created successfully.');
    }
}
