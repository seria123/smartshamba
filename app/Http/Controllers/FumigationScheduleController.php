<?php

namespace App\Http\Controllers;

use App\Models\ChemicalType;
use App\Models\CropCycle;
use App\Models\Farm;
use App\Models\Field;
use App\Models\FumigationLog;
use App\Models\FumigationNotification;
use App\Models\FumigationPhoto;
use App\Models\FumigationSchedule;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FumigationScheduleController extends Controller
{
    public function index()
    {
        $schedules = FumigationSchedule::with(['farm', 'field', 'cropCycle', 'chemical', 'operator'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        $activeFumigations = FumigationSchedule::where('user_id', Auth::id())
            ->whereIn('status', [FumigationSchedule::STATUS_ACTIVE, FumigationSchedule::STATUS_VENTILATING])
            ->count();

        return view('fumigation_schedules.index', compact('schedules', 'activeFumigations'));
    }

    public function dashboard()
    {
        $userId = Auth::id();

        $activeFumigations = FumigationSchedule::with(['chemical', 'field', 'farm'])
            ->where('user_id', $userId)
            ->whereIn('status', [
                FumigationSchedule::STATUS_ACTIVE,
                FumigationSchedule::STATUS_VENTILATING
            ])
            ->get();

        $upcoming = FumigationSchedule::where('user_id', $userId)
            ->where('status', FumigationSchedule::STATUS_SCHEDULED)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        $unsafeConditions = FumigationSchedule::where('user_id', $userId)
            ->where('status', FumigationSchedule::STATUS_SCHEDULED)
            ->where(function ($q) {
                $q->where('temperature', '<', 15)
                  ->orWhere('temperature', '>', 35)
                  ->orWhere('humidity_level', '>', 90)
                  ->orWhere('wind_speed', '>', 15);
            })
            ->get();

        $incompletePpe = FumigationSchedule::where('user_id', $userId)
            ->where('ppe_complete', false)
            ->whereIn('status', [
                FumigationSchedule::STATUS_ACTIVE,
                FumigationSchedule::STATUS_VENTILATING
            ])
            ->get();

        return view('fumigation_schedules.dashboard', compact(
            'activeFumigations', 'upcoming', 'unsafeConditions', 'incompletePpe'
        ));
    }

    public function create()
    {
        $farms = Farm::where('user_id', Auth::id())->get();
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field.farm', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();
        $chemicalTypes = ChemicalType::all();
        $staff = Staff::whereHas('farm', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();

        return view('fumigation_schedules.create', compact(
            'farms', 'fields', 'cropCycles', 'chemicalTypes', 'staff'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'target_pest' => 'required|string|max:255',
            'fumigation_type' => 'required|in:soil,storage,greenhouse',
            'chemical_id' => 'nullable|exists:chemical_types,id',
            'chemical_form' => 'required|in:Gas,Tablet,Pellet',
            'active_ingredient' => 'required|string|max:255',
            'mode_of_action' => 'required|string|max:255',
            'toxicity_level' => 'nullable|in:low,medium,high,critical',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'exposure_duration_hours' => 'required|integer|min:1',
            'frequency_days' => 'nullable|integer|min:1',
            'status' => 'required|in:scheduled,active,ventilating,completed,missed,cancelled',
            'temperature' => 'nullable|numeric|min:-50|max:100',
            'humidity_level' => 'nullable|numeric|min:0|max:100',
            'wind_speed' => 'nullable|numeric|min:0',
            'location' => 'required|in:field,store,greenhouse',
            'area_covered' => 'nullable|numeric|min:0',
            'volume_covered' => 'nullable|numeric|min:0',
            'dosage' => 'nullable|string|max:255',
            'application_method' => 'nullable|string|max:255',
            'performed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'enclosure_type' => 'nullable|string|max:255',
            'sealing_status' => 'nullable|in:sealed,not_sealed',
            'ventilation_method' => 'nullable|in:natural,forced,mechanical',
            'rei_hours' => 'nullable|integer|min:0',
            'safe_entry_at' => 'nullable|date',
            'staff_operator_id' => 'nullable|exists:staff,id',
            'operator_certified' => 'nullable|boolean',
            'certification_expiry' => 'nullable|date',
            'emergency_contacts' => 'nullable|string',
            'ppe_respirator' => 'boolean',
            'ppe_gloves' => 'boolean',
            'ppe_suit' => 'boolean',
            'pest_activity_before' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['ppe_complete'] = ($validated['ppe_respirator'] ?? false)
            && ($validated['ppe_gloves'] ?? false)
            && ($validated['ppe_suit'] ?? false);

        if ($validated['status'] === FumigationSchedule::STATUS_ACTIVE
            && !$validated['ppe_complete']) {
            return back()->withInput()->withErrors([
                'ppe_complete' => 'Complete PPE checklist before starting fumigation.'
            ]);
        }

        $chem = null;
        if (!empty($validated['chemical_id'])) {
            $chem = ChemicalType::find($validated['chemical_id']);
            if ($chem) {
                $validated['active_ingredient'] = $chem->name;
                $validated['toxicity_level'] = $chem->toxicity_level ?? $validated['toxicity_level'];
                $this->autoCalculateDosage($validated, $chem);
            }
        }

        $this->evaluateEnvironmentalConditions($validated);
        $this->generateAiRecommendations($validated, $chem);

        if ($validated['status'] === FumigationSchedule::STATUS_ACTIVE
            && isset($validated['rei_hours'])) {
            $validated['actual_start_at'] = now();
            $validated['safe_entry_at'] = now()->addHours($validated['rei_hours']);
        }

        $schedule = FumigationSchedule::create($validated);

        $this->logAction($schedule, 'schedule_created', 'info', 'Fumigation schedule created');
        $this->sendNotification($schedule, 'new_schedule', 'app');

        return redirect()->route('fumigation_schedules.index')
            ->with('success', 'Fumigation schedule created successfully.');
    }

    public function show(FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);
        $fumigationSchedule->load(['chemical', 'operator', 'photos', 'logs', 'notifications']);

        return view('fumigation_schedules.show', compact('fumigationSchedule'));
    }

    public function edit(FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);
        $farms = Farm::where('user_id', Auth::id())->get();
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field.farm', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();
        $chemicalTypes = ChemicalType::all();
        $staff = Staff::whereHas('farm', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();

        return view('fumigation_schedules.edit', compact(
            'fumigationSchedule', 'farms', 'fields', 'cropCycles', 'chemicalTypes', 'staff'
        ));
    }

    public function update(Request $request, FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);

        $validated = $request->validate([
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'target_pest' => 'required|string|max:255',
            'fumigation_type' => 'required|in:soil,storage,greenhouse',
            'chemical_id' => 'nullable|exists:chemical_types,id',
            'chemical_form' => 'required|in:Gas,Tablet,Pellet',
            'active_ingredient' => 'required|string|max:255',
            'mode_of_action' => 'required|string|max:255',
            'toxicity_level' => 'nullable|in:low,medium,high,critical',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'exposure_duration_hours' => 'required|integer|min:1',
            'frequency_days' => 'nullable|integer|min:1',
            'status' => 'required|in:scheduled,active,ventilating,completed,missed,cancelled',
            'temperature' => 'nullable|numeric|min:-50|max:100',
            'humidity_level' => 'nullable|numeric|min:0|max:100',
            'wind_speed' => 'nullable|numeric|min:0',
            'location' => 'required|in:field,store,greenhouse',
            'area_covered' => 'nullable|numeric|min:0',
            'volume_covered' => 'nullable|numeric|min:0',
            'dosage' => 'nullable|string|max:255',
            'application_method' => 'nullable|string|max:255',
            'performed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'enclosure_type' => 'nullable|string|max:255',
            'sealing_status' => 'nullable|in:sealed,not_sealed',
            'ventilation_method' => 'nullable|in:natural,forced,mechanical',
            'rei_hours' => 'nullable|integer|min:0',
            'safe_entry_at' => 'nullable|date',
            'actual_start_at' => 'nullable|date',
            'actual_end_at' => 'nullable|date',
            'pest_activity_after' => 'nullable|string|max:255',
            'effectiveness_rating' => 'nullable|in:0,25,50,75,100',
            'staff_operator_id' => 'nullable|exists:staff,id',
            'operator_certified' => 'nullable|boolean',
            'certification_expiry' => 'nullable|date',
            'emergency_contacts' => 'nullable|string',
            'ppe_respirator' => 'boolean',
            'ppe_gloves' => 'boolean',
            'ppe_suit' => 'boolean',
        ]);

        $validated['ppe_complete'] = ($validated['ppe_respirator'] ?? false)
            && ($validated['ppe_gloves'] ?? false)
            && ($validated['ppe_suit'] ?? false);

        if (!empty($validated['chemical_id'])) {
            $chem = ChemicalType::find($validated['chemical_id']);
            if ($chem) {
                $validated['active_ingredient'] = $chem->name;
                $validated['toxicity_level'] = $chem->toxicity_level ?? $validated['toxicity_level'];
                $this->autoCalculateDosage($validated, $chem);
            }
        }

        if (isset($validated['status']) && $validated['status'] === FumigationSchedule::STATUS_COMPLETED) {
            $validated['actual_end_at'] = now();
            if (isset($validated['rei_hours'])) {
                $validated['safe_entry_at'] = now()->addHours($validated['rei_hours']);
            }
        }

        if (isset($validated['status']) && $validated['status'] === FumigationSchedule::STATUS_ACTIVE) {
            if (!$validated['ppe_complete']) {
                return back()->withInput()->withErrors([
                    'ppe_complete' => 'Complete PPE checklist before starting fumigation.'
                ]);
            }
            $validated['actual_start_at'] = now();
            if (isset($validated['rei_hours'])) {
                $validated['safe_entry_at'] = now()->addHours($validated['rei_hours']);
            }
        }

        $fumigationSchedule->update($validated);

        $this->logAction($fumigationSchedule, 'schedule_updated', 'info', 'Schedule updated');

        return redirect()->route('fumigation_schedules.show', $fumigationSchedule->id)
            ->with('success', 'Fumigation schedule updated successfully.');
    }

    public function startFumigation(FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);

        if (!$fumigationSchedule->ppe_complete) {
            return back()->with('error', 'Complete PPE checklist before starting.');
        }

        if ($fumigationSchedule->sealing_status !== 'sealed' && $fumigationSchedule->location !== 'field') {
            return back()->with('error', 'Seal the enclosure before starting fumigation.');
        }

        $fumigationSchedule->update([
            'status' => FumigationSchedule::STATUS_ACTIVE,
            'actual_start_at' => now(),
            'safe_entry_at' => now()->addHours($fumigationSchedule->rei_hours ?? 24),
        ]);

        $this->logAction($fumigationSchedule, 'fumigation_started', 'warning', 'Fumigation started');
        $this->sendNotification($fumigationSchedule, 'fumigation_started', 'app');

        return redirect()->route('fumigation_schedules.show', $fumigationSchedule->id)
            ->with('success', 'Fumigation started. DO NOT ENTER until safe entry time.');
    }

    public function completeFumigation(FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);

        $fumigationSchedule->update([
            'status' => FumigationSchedule::STATUS_COMPLETED,
            'actual_end_at' => now(),
        ]);

        $this->logAction($fumigationSchedule, 'fumigation_completed', 'success', 'Fumigation completed');
        $this->sendNotification($fumigationSchedule, 'ventilation_required', 'app');

        return redirect()->route('fumigation_schedules.show', $fumigationSchedule->id)
            ->with('success', 'Fumigation marked as completed. Ventilation required.');
    }

    public function startVentilation(FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);

        $fumigationSchedule->update([
            'status' => FumigationSchedule::STATUS_VENTILATING,
        ]);

        $this->logAction($fumigationSchedule, 'ventilation_started', 'info', 'Ventilation started');
        $this->sendNotification($fumigationSchedule, 'ventilation_required', 'app');

        return redirect()->route('fumigation_schedules.show', $fumigationSchedule->id)
            ->with('success', 'Ventilation started. Area is being cleared.');
    }

    public function uploadPhoto(Request $request, FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);

        $validated = $request->validate([
            'type' => 'required|in:before_sealing,after_ventilation,incident,leaks',
            'photo' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:500',
        ]);

        $path = $request->file('photo')->store('fumigation-photos', 'public');

        FumigationPhoto::create([
            'fumigation_schedule_id' => $fumigationSchedule->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'path' => $path,
            'caption' => $validated['caption'] ?? null,
        ]);

        $this->logAction($fumigationSchedule, 'photo_uploaded', 'info', "Photo uploaded: {$validated['type']}");

        return back()->with('success', 'Photo uploaded successfully.');
    }

    public function destroy(FumigationSchedule $fumigationSchedule)
    {
        $this->authorizeOwnership($fumigationSchedule);
        $fumigationSchedule->delete();

        return redirect()->route('fumigation_schedules.index')
            ->with('success', 'Fumigation schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    private function autoCalculateDosage(array &$validated, ?ChemicalType $chem): void
    {
        if (!$chem) return;

        if ($validated['fumigation_type'] === 'storage' && !empty($validated['volume_covered'])) {
            $validated['dosage'] = $chem->dosage_per_m3
                ? "{$chem->dosage_per_m3} tablets/m³ x {$validated['volume_covered']} m³"
                : ($validated['dosage'] ?? 'Calculate manually');
        } elseif (($validated['fumigation_type'] === 'soil' || $validated['fumigation_type'] === 'greenhouse')
            && !empty($validated['area_covered'])) {
            $validated['dosage'] = $chem->dosage_per_hectare
                ? "{$chem->dosage_per_hectare} per ha x {$validated['area_covered']} ha"
                : ($validated['dosage'] ?? 'Calculate manually');
        }
    }

    private function evaluateEnvironmentalConditions(array &$validated): void
    {
        $unsafe = false;
        $warnings = [];

        if (isset($validated['temperature']) && $validated['temperature'] !== null) {
            if ($validated['temperature'] < 10 || $validated['temperature'] > 40) {
                $unsafe = true;
                $warnings[] = "Temperature ({$validated['temperature']}°C) outside safe range (10-40°C)";
            }
        }

        if (isset($validated['humidity_level']) && $validated['humidity_level'] !== null) {
            if ($validated['humidity_level'] > 90) {
                $unsafe = true;
                $warnings[] = "High humidity ({$validated['humidity_level']}%) may reduce fumigant effectiveness";
            }
        }

        if (isset($validated['wind_speed']) && $validated['wind_speed'] !== null) {
            if ($validated['wind_speed'] > 15) {
                $unsafe = true;
                $warnings[] = "High wind speed ({$validated['wind_speed']} m/s) – unsafe for outdoor fumigation";
            }
        }

        if ($unsafe) {
            $this->sendNotification(null, 'unsafe_conditions', 'app', implode('; ', $warnings));
        }
    }

    private function generateAiRecommendations(array &$validated, ?ChemicalType $chem): void
    {
        $risk = 'low';
        $suggestion = null;

        if (!empty($validated['area_covered']) && !empty($validated['volume_covered'])) {
            $risk = 'medium';
            $suggestion = 'Large area detected. Consider splitting treatment into zones.';
        }

        if (!empty($chem) && $chem->toxicity_level === 'critical') {
            $risk = 'high';
            $suggestion = ($suggestion ? $suggestion . ' ' : '') . 'Critical toxicity chemical. Ensure operator certification is up to date.';
        }

        if ($validated['sealing_status'] === 'not_sealed' && $validated['fumigation_type'] !== 'soil') {
            $risk = $risk === 'high' ? 'high' : 'medium';
            $suggestion = ($suggestion ? $suggestion . ' ' : '') . 'Seal the enclosure for effective fumigation; otherwise gas will escape and treatment will fail.';
        }

        $validated['ai_recommended'] = true;
        $validated['infestation_risk'] = $risk;
        $validated['ai_suggestion'] = $suggestion;
    }

    private function logAction($schedule, string $action, string $status, ?string $notes = null): void
    {
        FumigationLog::create([
            'fumigation_schedule_id' => $schedule->id,
            'user_id' => $schedule->user_id,
            'action' => $action,
            'status' => $status,
            'notes' => $notes,
            'occurred_at' => now(),
        ]);
    }

    private function sendNotification($scheduleOrNull, string $type, string $channel, ?string $message = null): void
    {
        $userId = $scheduleOrNull?->user_id ?? Auth::id();
        $scheduleId = $scheduleOrNull?->id ?? null;

        $defaults = [
            'new_schedule' => 'New fumigation schedule created',
            'fumigation_started' => 'Fumigation started. DO NOT ENTER!',
            'ventilation_required' => 'Ventilation required',
            'unsafe_conditions' => 'Unsafe conditions for fumigation',
            'safe_to_enter' => 'Safe to re-enter area',
        ];

        FumigationNotification::create([
            'fumigation_schedule_id' => $scheduleId,
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'status' => 'pending',
            'message' => $message ?? $defaults[$type] ?? $type,
        ]);
    }
}
