@extends('layouts.MainLayout')

@section('title', 'Fumigation Dashboard - SmartShamba')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Fumigation Dashboard</h1>
        <p class="text-sm text-gray-500 mt-2">Monitor active fumigation, environmental conditions, and safety status</p>
    </div>

    @if($activeFumigations->count() > 0)
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
            <div class="flex items-center space-x-3 mb-4">
                <span class="relative flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                </span>
                <h2 class="text-xl font-bold text-red-800">Active Fumigation in Progress</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeFumigations as $f)
                    <div class="bg-white rounded-lg border-2 border-red-200 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                                @if($f->status === 'ventilating') bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($f->status) }}
                            </span>
                            <a href="{{ route('fumigation_schedules.show', $f->id) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">View</a>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($f->fumigation_type) }} - {{ $f->target_pest }}</p>
                        <p class="text-xs text-gray-600 mt-1">Location: {{ ucfirst($f->location) }}</p>
                        <p class="text-xs text-gray-600">Chemical: {{ $f->chemical->name ?? $f->active_ingredient ?? 'N/A' }}</p>

                        @if($f->status === FumigationSchedule::STATUS_ACTIVE && $f->actual_start_at)
                            <div class="mt-3 pt-3 border-t border-red-200">
                                @if($f->safe_entry_at && $f->safe_entry_at->isFuture())
                                    <p class="text-xs font-bold text-red-700">⏳ Safe entry in: {{ $f->safe_entry_at->diffForHumans() }}</p>
                                    <p class="text-xs text-red-600">Do NOT enter until {{ $f->safe_entry_at->format('H:i') }}</p>
                                @elseif($f->safe_entry_at && !$f->safe_entry_at->isFuture())
                                    <p class="text-xs font-bold text-green-700">✅ Safe to re-enter</p>
                                @endif
                            </div>
                        @endif

                        <div class="mt-3 flex space-x-2">
                            @if($f->status === FumigationSchedule::STATUS_ACTIVE)
                                <form action="{{ route('fumigation_schedules.complete', $f->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-xs bg-orange-500 text-white px-3 py-1 rounded-lg">Complete</button>
                                </form>
                            @endif
                            @if($f->status !== FumigationSchedule::STATUS_VENTILATING)
                                <form action="{{ route('fumigation_schedules.ventilate', $f->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-xs bg-blue-500 text-white px-3 py-1 rounded-lg">Ventilate</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-bold text-gray-600 uppercase">Upcoming Schedules</h3>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $upcoming->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-bold text-gray-600 uppercase">Unsafe Condition Alerts</h3>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $unsafeConditions->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-bold text-gray-600 uppercase">PPE Incomplete</h3>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $incompletePpe->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Upcoming Schedules</h2>
            <a href="{{ route('fumigation_schedules.index') }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">View All</a>
        </div>
        @if($upcoming->count() > 0)
            <div class="space-y-3">
                @foreach($upcoming as $u)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($u->fumigation_type) }} - {{ $u->target_pest }}</p>
                            <p class="text-xs text-gray-600">{{ $u->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-lg bg-yellow-100 text-yellow-800">
                            {{ ucfirst($u->sealing_status ?? 'Not sealed') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No upcoming schedules.</p>
        @endif
    </div>

    @if($unsafeConditions->count() > 0)
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-red-800 mb-4">⚠️ Unsafe Environmental Conditions</h2>
            <div class="space-y-3">
                @foreach($unsafeConditions as $ua)
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($ua->fumigation_type) }} - {{ $ua->target_pest }}</p>
                            <p class="text-xs text-gray-600">Scheduled: {{ $ua->scheduled_date?->format('M d, Y') }}</p>
                            @if($ua->temperature)
                                <p class="text-xs text-red-600">Temp: {{ $ua->temperature }}°C</p>
                            @endif
                            @if($ua->humidity_level)
                                <p class="text-xs text-red-600">Humidity: {{ $ua->humidity_level }}%</p>
                            @endif
                        </div>
                        <a href="{{ route('fumigation_schedules.show', $ua->id) }}" class="text-xs bg-red-600 text-white px-3 py-1 rounded-lg">Review</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
