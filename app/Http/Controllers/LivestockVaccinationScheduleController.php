<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\LivestockVaccinationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LivestockVaccinationScheduleController extends Controller
{
    public function index()
    {
        $schedules = LivestockVaccinationSchedule::with(['livestock', 'livestockType'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('livestock_vaccination_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $livestocks = Livestock::where('user_id', Auth::id())->whereIn('status', ['healthy', 'sick'])->get();
        $livestockTypes = LivestockType::all();

        return view('livestock_vaccination_schedules.create', compact('livestocks', 'livestockTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'nullable|exists:livestock,id',
            'livestock_type_id' => 'nullable|exists:livestock_types,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'animal_weight' => 'nullable|numeric|min:0',
            'route' => 'nullable|in:IM,SC,oral,intradermal,nasal',
            'dose_amount' => 'nullable|numeric|min:0',
            'dose_unit' => 'nullable|string|max:20',
            'administered_date' => 'nullable|date',
            'administered_by' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'evidence_photo_path' => 'nullable|image|max:2048',
            'qr_code_data' => 'nullable|string|max:255',
            'vet_notes' => 'nullable|string',
            'location' => 'nullable|string|max:100',
            'season' => 'nullable|string|max:50',
            'is_bulk_entry' => 'boolean',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
            'status' => 'required|in:scheduled,administered,missed,cancelled',
            'user_role' => 'nullable|in:admin,vet,worker',
            'vet_approved_by' => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = Auth::id();
        if (empty($validated['user_role'])) {
            $validated['user_role'] = LivestockVaccinationSchedule::USER_ROLE_WORKER;
        }
        if (empty($validated['alert_status'])) {
            $validated['alert_status'] = LivestockVaccinationSchedule::ALERT_STATUS_NONE;
        }

        if ($request->hasFile('evidence_photo_path')) {
            $validated['evidence_photo_path'] = $request->file('evidence_photo_path')->store('vaccination-evidence', 'public');
        }

        LivestockVaccinationSchedule::create($validated);

        return redirect()->route('livestock_vaccination_schedules.index')
            ->with('success', 'Vaccination schedule created successfully.');
    }

    public function show(LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);
        return view('livestock_vaccination_schedules.show', compact('livestockVaccinationSchedule'));
    }

    public function edit(LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);
        $livestocks = Livestock::where('user_id', Auth::id())->whereIn('status', ['healthy', 'sick'])->get();
        $livestockTypes = LivestockType::all();

        return view('livestock_vaccination_schedules.edit', compact('livestockVaccinationSchedule', 'livestocks', 'livestockTypes'));
    }

    public function update(Request $request, LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);

        $validated = $request->validate([
            'livestock_id' => 'nullable|exists:livestock,id',
            'livestock_type_id' => 'nullable|exists:livestock_types,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'animal_weight' => 'nullable|numeric|min:0',
            'route' => 'nullable|in:IM,SC,oral,intradermal,nasal',
            'dose_amount' => 'nullable|numeric|min:0',
            'dose_unit' => 'nullable|string|max:20',
            'administered_date' => 'nullable|date',
            'administered_by' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'evidence_photo_path' => 'nullable|image|max:2048',
            'qr_code_data' => 'nullable|string|max:255',
            'vet_notes' => 'nullable|string',
            'location' => 'nullable|string|max:100',
            'season' => 'nullable|string|max:50',
            'is_bulk_entry' => 'boolean',
            'parent_schedule_id' => 'nullable|exists:livestock_vaccination_schedules,id',
            'cost' => 'nullable|numeric|min:0',
            'next_due_date' => 'nullable|date',
            'status' => 'required|in:scheduled,administered,missed,cancelled',
            'alert_status' => 'nullable|in:none,upcoming,due_soon,overdue,contraindication,expired_vaccine',
            'last_synced_at' => 'nullable|date',
            'user_role' => 'nullable|in:admin,vet,worker',
            'vet_approved_by' => 'nullable|exists:users,id',
            'vet_approved_at' => 'nullable|date',
        ]);

        // Set default values if not provided
        if (empty($validated['alert_status'])) {
            $validated['alert_status'] = LivestockVaccinationSchedule::ALERT_STATUS_NONE;
        }
        if (empty($validated['user_role'])) {
            $validated['user_role'] = LivestockVaccinationSchedule::USER_ROLE_WORKER;
        }

        if ($request->hasFile('evidence_photo_path')) {
            if ($livestockVaccinationSchedule->evidence_photo_path) {
                Storage::disk('public')->delete($livestockVaccinationSchedule->evidence_photo_path);
            }
            $validated['evidence_photo_path'] = $request->file('evidence_photo_path')->store('vaccination-evidence', 'public');
        }

        $livestockVaccinationSchedule->update($validated);

        return redirect()->route('livestock_vaccination_schedules.index')
            ->with('success', 'Vaccination schedule updated successfully.');
    }

    public function destroy(LivestockVaccinationSchedule $livestockVaccinationSchedule)
    {
        $this->authorizeOwnership($livestockVaccinationSchedule);
        $livestockVaccinationSchedule->delete();

        return redirect()->route('livestock_vaccination_schedules.index')
            ->with('success', 'Vaccination schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}