@extends('layouts.MainLayout')

@section('title', 'Fumigation Schedule Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Fumigation Schedule Details</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('fumigation_schedules.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="{{ route('fumigation_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
            </a>
        </div>
    </div>

    @if(in_array($fumigationSchedule->status, ['active', 'ventilating']))
        <div class="@if($fumigationSchedule->status === 'active') bg-red-50 border-2 border-red-200 @else bg-blue-50 border-2 border-blue-200 @endif rounded-xl p-6">
            <div class="flex items-center space-x-3">
                @if($fumigationSchedule->status === 'active')
                    <span class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                    </span>
                    <h2 class="text-xl font-bold @if($fumigationSchedule->status === 'active') text-red-800 @else text-blue-800 @endif">
                        @if($fumigationSchedule->status === 'active') 🔴 ACTIVE FUMIGATION IN PROGRESS @else 🌬️ VENTILATING @endif
                    </h2>
                @else
                    <h2 class="text-xl font-bold text-blue-800">🌬️ VENTILATING</h2>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500">Chemical</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $fumigationSchedule->chemical->name ?? $fumigationSchedule->active_ingredient ?? 'N/A' }}</p>
                    <p class="text-xs text-red-600 mt-1">Toxicity: {{ ucfirst($fumigationSchedule->toxicity_level ?? 'N/A') }}</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500">Target Pest</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->target_pest) }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ ucfirst($fumigationSchedule->fumigation_type) }} Fumigation</p>
                </div>
                @if($fumigationSchedule->safe_entry_at)
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">
                            @if($fumigationSchedule->safe_entry_at->isFuture())
                                Safe Entry In
                            @else
                                Safe Entry Time
                            @endif
                        </p>
                        <p class="text-lg font-semibold @if($fumigationSchedule->safe_entry_at->isFuture()) text-red-700 @else text-green-700 @endif">
                            @if($fumigationSchedule->safe_entry_at->isFuture())
                                ⏳ {{ $fumigationSchedule->safe_entry_at->diffForHumans() }}
                            @else
                                ✅ {{ $fumigationSchedule->safe_entry_at->format('H:i') }}
                            @endif
                        </p>
                        <p class="text-xs @if($fumigationSchedule->safe_entry_at->isFuture()) text-red-600 @else text-green-600 @endif">
                            @if($fumigationSchedule->safe_entry_at->isFuture())
                                Do NOT enter until {{ $fumigationSchedule->safe_entry_at->format('H:i') }}
                            @else
                                Area is safe for re-entry
                            @endif
                        </p>
                    </div>
                @endif
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500">REI (Re-entry Interval)</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $fumigationSchedule->rei_hours ?? 24 }} hours</p>
                </div>
            </div>

            <div class="mt-4 flex space-x-3">
                @if($fumigationSchedule->status === 'active')
                    <form action="{{ route('fumigation_schedules.complete', $fumigationSchedule->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-check mr-2"></i>Complete Fumigation
                        </button>
                    </form>
                @endif
                @if($fumigationSchedule->status !== 'ventilating')
                    <form action="{{ route('fumigation_schedules.ventilate', $fumigationSchedule->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-wind mr-2"></i>Start Ventilation
                        </button>
                    </form>
                @endif
                <a href="{{ route('fumigation_schedules.edit', $fumigationSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Fumigation Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><h3 class="text-sm font-medium text-gray-500">Target Pest</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->target_pest) }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Fumigation Type</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->fumigation_type) }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Farm</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->farm->name ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Field</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->field->name ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Chemical</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->chemical->name ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Chemical Form</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->chemical_form }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Active Ingredient</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->active_ingredient }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Toxicity Level</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->toxicity_level) }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Quantity</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->quantity }} {{ $fumigationSchedule->unit }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Status</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->status) }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Location & Environment</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><h3 class="text-sm font-medium text-gray-500">Location</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->location) }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Area Covered</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->area_covered ?? 'N/A' }} ha/acres</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Volume Covered</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->volume_covered ?? 'N/A' }} m³</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Sealing Status</h3>
                    <p class="text-base font-semibold @if($fumigationSchedule->sealing_status === 'sealed') text-green-700 @else text-red-700 @endif">
                        {{ ucfirst(str_replace('_', ' ', $fumigationSchedule->sealing_status ?? 'N/A')) }}
                    </p>
                </div>
                <div><h3 class="text-sm font-medium text-gray-500">Enclosure Type</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->enclosure_type ?? 'N/A') }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Temperature</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->temperature ?? 'N/A' }}°C</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Humidity</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->humidity_level ?? 'N/A' }}%</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Wind Speed</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->wind_speed ?? 'N/A' }} m/s</p></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Safety Management</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><h3 class="text-sm font-medium text-gray-500">Operator</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->operator->fullName ?? ($fumigationSchedule->performed_by ?? 'N/A') }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Certified</h3>
                    <p class="text-base font-semibold @if($fumigationSchedule->operator_certified) text-green-700 @else text-red-700 @endif">
                        {{ $fumigationSchedule->operator_certified ? 'Yes' : 'No / Unknown' }}
                    </p>
                </div>
                <div><h3 class="text-sm font-medium text-gray-500">Emergency Contacts</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->emergency_contacts ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">PPE Complete</h3>
                    <p class="text-base font-semibold @if($fumigationSchedule->ppe_complete) text-green-700 @else text-red-700 @endif">
                        {{ $fumigationSchedule->ppe_complete ? 'Yes' : 'No' }}
                    </p>
                </div>
                <div><h3 class="text-sm font-medium text-gray-500">Respirator Mask</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->ppe_respirator ? '✅ Worn' : '❌ Not worn' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Protective Gloves</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->ppe_gloves ? '✅ Worn' : '❌ Not worn' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Protective Suit</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->ppe_suit ? '✅ Worn' : '❌ Not worn' }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Timing & Effectiveness</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Start Time</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->start_time?->format('H:i') ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">End Time</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->end_time?->format('H:i') ?? 'N/A' }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Duration</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->exposure_duration_hours }} hours</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">REI (Re-entry Interval)</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->rei_hours ?? 'N/A' }} hours</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Ventilation Method</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->ventilation_method ?? 'N/A') }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Pest Activity Before</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->pest_activity_before ?? 'N/A') }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Pest Activity After</h3><p class="text-base font-semibold text-gray-900">{{ ucfirst($fumigationSchedule->pest_activity_after ?? 'N/A') }}</p></div>
                <div><h3 class="text-sm font-medium text-gray-500">Effectiveness Rating</h3>
                    <p class="text-base font-semibold text-gray-900">
                        @if($fumigationSchedule->effectiveness_rating)
                            {{ $fumigationSchedule->effectiveness_rating }}% @if($fumigationSchedule->effectiveness_rating >= 75) ✅ @elseif($fumigationSchedule->effectiveness_rating >= 50) ⚠️ @else ❌ @endif
                        @else N/A @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($fumigationSchedule->ai_recommended)
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-purple-800 mb-3">🧠 AI Recommendations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-700">Infestation Risk</h3>
                    <p class="text-base font-semibold @if($fumigationSchedule->infestation_risk === 'high') text-red-700 @elseif($fumigationSchedule->infestation_risk === 'medium') text-yellow-700 @else text-green-700 @endif">
                        {{ ucfirst($fumigationSchedule->infestation_risk ?? 'N/A') }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700">Suggestion</h3>
                    <p class="text-base text-gray-900">{{ $fumigationSchedule->ai_suggestion ?: 'None' }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Additional Details</h2>
        <div class="grid grid-cols-2 gap-4">
            <div><h3 class="text-sm font-medium text-gray-500">Dosage & Application</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->dosage ?? 'N/A' }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500">Application Method</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->application_method ?? 'N/A' }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500">Performed By</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->performed_by ?? 'N/A' }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500">Cost</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->cost ? 'KES ' . number_format($fumigationSchedule->cost, 2) : 'N/A' }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500">Completed Date</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->completed_date?->format('M d, Y') ?? 'N/A' }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500">Next Schedule</h3><p class="text-base font-semibold text-gray-900">{{ $fumigationSchedule->next_schedule_date?->format('M d, Y') ?? 'N/A' }}</p></div>
        </div>

        @if($fumigationSchedule->notes)
            <div class="mt-4"><h3 class="text-sm font-medium text-gray-500">Notes</h3><p class="text-gray-900">{{ $fumigationSchedule->notes }}</p></div>
        @endif
        @if($fumigationSchedule->operator_notes)
            <div class="mt-4"><h3 class="text-sm font-medium text-gray-500">Operator Notes</h3><p class="text-gray-900">{{ $fumigationSchedule->operator_notes }}</p></div>
        @endif
        @if($fumigationSchedule->incident_report)
            <div class="mt-4"><h3 class="text-sm font-medium text-gray-500">Incident Report</h3><p class="text-red-700">{{ $fumigationSchedule->incident_report }}</p></div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Photos</h2>
        @if($fumigationSchedule->photos->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($fumigationSchedule->photos as $photo)
                    <div class="border rounded-lg overflow-hidden">
                        <img src="{{ Storage::url($photo->path) }}" alt="{{ $photo->type }}" class="w-full h-32 object-cover">
                        <p class="text-xs text-gray-600 p-2">{{ ucfirst(str_replace('_', ' ', $photo->type)) }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No photos uploaded.</p>
        @endif

        <form action="{{ route('fumigation_schedules.photos.store', $fumigationSchedule->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg" required>
                    <option value="before_sealing">Before Sealing</option>
                    <option value="after_ventilation">After Ventilation</option>
                    <option value="incident">Incident</option>
                    <option value="leaks">Leak Detected</option>
                </select>
                <input type="file" name="photo" accept="image/*" class="px-3 py-2 border border-gray-300 rounded-lg" required>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Upload Photo</button>
            </div>
            <input type="text" name="caption" placeholder="Photo caption (optional)" class="w-full mt-3 px-3 py-2 border border-gray-300 rounded-lg">
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Activity Logs</h2>
        @if($fumigationSchedule->logs->count() > 0)
            <div class="space-y-3">
                @foreach($fumigationSchedule->logs->take(10) as $log)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold
                            @if($log->status === 'success') bg-green-100 text-green-800
                            @elseif($log->status === 'warning') bg-red-100 text-red-800
                            @elseif($log->status === 'info') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($log->status) }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                            <p class="text-xs text-gray-600">{{ $log->notes ?? 'No notes' }}</p>
                            <p class="text-xs text-gray-500">{{ $log->occurred_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No logs recorded yet.</p>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Notifications</h2>
        @if($fumigationSchedule->notifications->count() > 0)
            <div class="space-y-2">
                @foreach($fumigationSchedule->notifications->take(5) as $notif)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $notif->type)) }}</p>
                            <p class="text-xs text-gray-600">{{ $notif->message }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">{{ ucfirst($notif->channel) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No notifications yet.</p>
        @endif
    </div>

    <div class="mt-8 flex space-x-4">
        <a href="{{ route('fumigation_schedules.edit', $fumigationSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <form action="{{ route('fumigation_schedules.destroy', $fumigationSchedule->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        </form>
    </div>
</div>
@endsection
