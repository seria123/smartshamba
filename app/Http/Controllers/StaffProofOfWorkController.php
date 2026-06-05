<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Staff;
use App\Models\StaffProofOfWork;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffProofOfWorkController extends Controller
{
    public function index(Staff $staff): View
    {
        $proofs = $staff->proofsOfWork()
            ->with(['task', 'field'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.proofs.index', compact('staff', 'proofs'));
    }

    public function create(Staff $staff): View
    {
        $tasks = $staff->tasks()->where('status', Task::STATUS_IN_PROGRESS)->get();
        $fields = $staff->activeFieldAssignments()->with('field')->get()->pluck('field');

        return view('staff.proofs.create', compact('staff', 'tasks', 'fields'));
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'field_id' => 'nullable|exists:fields,id',
            'proof_type' => 'required|string|max:100',
            'photo' => 'required|image|max:5120',
            'description' => 'nullable|string',
            'taken_at' => 'nullable|date',
        ]);

        $path = $request->file('photo')->store('proof-of-work', 'public');

        StaffProofOfWork::create([
            'staff_id' => $staff->id,
            'task_id' => $validated['task_id'],
            'field_id' => $validated['field_id'],
            'farm_id' => $staff->farm_id,
            'proof_type' => $validated['proof_type'],
            'photo_path' => $path,
            'original_filename' => $request->file('photo')->getClientOriginalName(),
            'description' => $validated['description'] ?? null,
            'taken_at' => $validated['taken_at'] ?? now(),
            'status' => 'pending',
        ]);

        return redirect()->route('staff.proofs.index', $staff)
            ->with('success', 'Proof of work uploaded successfully.');
    }

    public function approve(Staff $staff, StaffProofOfWork $proof): RedirectResponse
    {
        $proof->update([
            'status' => 'approved',
            'reviewed_by' => $staff->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Proof of work approved.');
    }

    public function reject(Request $request, Staff $staff, StaffProofOfWork $proof): RedirectResponse
    {
        $request->validate([
            'review_notes' => 'required|string',
        ]);

        $proof->update([
            'status' => 'rejected',
            'reviewed_by' => $staff->id,
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Proof of work rejected.');
    }
}
