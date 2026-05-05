<?php

namespace App\Modules\Workers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Http\Controllers\Concerns\ValidatesLabourScope;
use App\Modules\Workers\Models\LabourAttendanceRecord;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LabourAttendanceController extends Controller
{
    use ValidatesLabourScope;

    public function index(): View
    {
        return view('labour::attendance.index', [
            'records' => LabourAttendanceRecord::query()
                ->with(['organization', 'farm', 'worker', 'team', 'recorder'])
                ->latest('date')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('labour::attendance.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['recorded_by'] = $request->user()->id;

        LabourAttendanceRecord::query()->create($data);

        return redirect()->route('labour.attendance.index')->with('status', 'Attendance recorded.');
    }

    public function edit(LabourAttendanceRecord $attendance): View
    {
        return view('labour::attendance.form', $this->formData($attendance));
    }

    public function update(Request $request, LabourAttendanceRecord $attendance): RedirectResponse
    {
        $attendance->update($this->validated($request));

        return redirect()->route('labour.attendance.index')->with('status', 'Attendance updated.');
    }

    private function formData(?LabourAttendanceRecord $attendance = null): array
    {
        return [
            'attendance' => $attendance,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'workers' => LabourWorker::query()->with('farm')->orderBy('name')->get(),
            'teams' => LabourTeam::query()->with('farm')->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'labour_worker_id' => ['required', 'integer', Rule::exists('labour_workers', 'id')],
            'labour_team_id' => ['nullable', 'integer', Rule::exists('labour_teams', 'id')],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(['present', 'absent', 'leave', 'sick', 'half-day'])],
            'check_in_at' => ['nullable', 'date_format:H:i'],
            'check_out_at' => ['nullable', 'date_format:H:i', 'after:check_in_at'],
            'hours_worked' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        $this->ensureWorkerBelongsToFarm((int) $data['labour_worker_id'], (int) $data['farm_id']);
        $this->ensureTeamBelongsToFarm($data['labour_team_id'] ?? null, (int) $data['farm_id']);

        return $data;
    }
}
