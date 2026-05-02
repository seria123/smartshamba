<x-filament::page>
    <div class="space-y-6">
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="text-sm font-medium text-gray-500">Total Plantings</div>
                <div class="text-2xl font-bold text-gray-900">{{ $schedules->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="text-sm font-medium text-gray-500">Active Growing</div>
                <div class="text-2xl font-bold text-gray-900">{{ $schedules->whereIn('status', ['planted', 'growing', 'ready_for_harvest'])->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="text-sm font-medium text-gray-500">Ready for Harvest</div>
                <div class="text-2xl font-bold text-gray-900">{{ $schedules->where('status', 'ready_for_harvest')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-emerald-500">
                <div class="text-sm font-medium text-gray-500">Harvested</div>
                <div class="text-2xl font-bold text-gray-900">{{ $schedules->where('status', 'harvested')->count() }}</div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white rounded-lg shadow p-4">
            <div id="plantingCalendar" style="min-height: 600px;"></div>
        </div>

        <!-- Legend -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Legend</h3>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                    <span class="text-sm">Planted</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                    <span class="text-sm">Growing</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                    <span class="text-sm">Ready for Harvest</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span>
                    <span class="text-sm">Harvested</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-gray-400 mr-2"></span>
                    <span class="text-sm">Planned</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('plantingCalendar');
            
            const calendar = new FullCalendar.Calendar(container, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'standard',
                height: 'auto',
                aspectRatio: 1.8,
                events: [
                    @foreach($schedules as $schedule)
                    {
                        id: {{ $schedule->id }},
                        title: '{{ $schedule->crop->name }}',
                        start: '{{ $schedule->planting_date->format('Y-m-d') }}',
                        end: '{{ $schedule->expected_harvest_date ? $schedule->expected_harvest_date->format('Y-m-d') : null }}',
                        backgroundColor: '{{ $schedule->status === "planned" ? "#9CA3AF" : ($schedule->status === "planted" ? "#3B82F6" : ($schedule->status === "growing" ? "#10B981" : ($schedule->status === "ready_for_harvest" ? "#EAB308" : ($schedule->status === "harvested" ? "#059669" : "#EF4444")))) }}',
                        borderColor: '{{ $schedule->status === "planned" ? "#9CA3AF" : ($schedule->status === "planted" ? "#3B82F6" : ($schedule->status === "growing" ? "#10B981" : ($schedule->status === "ready_for_harvest" ? "#EAB308" : ($schedule->status === "harvested" ? "#059669" : "#EF4444")))) }}',
                        extendedProps: {
                            field: '{{ $schedule->field?->name ?? "N/A" }}',
                            farm: '{{ $schedule->farm?->name ?? "N/A" }}',
                            variety: '{{ $schedule->variety ?? "N/A" }}',
                            status: '{{ ucfirst(str_replace('_', ' ', $schedule->status)) }}',
                            progress: {{ $schedule->completion_percentage }},
                            harvest: '{{ $schedule->expected_harvest_date ? $schedule->expected_harvest_date->format('M j, Y') : "TBD" }}'
                        },
                        description: `
                            <strong>${'{{ $schedule->crop->name }}'}</strong><br>
                            Field: {{ $schedule->field?->name ?? 'N/A' }}<br>
                            Variety: {{ $schedule->variety ?? 'N/A' }}<br>
                            Status: {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}<br>
                            Progress: {{ $schedule->completion_percentage }}%<br>
                            Expected Harvest: {{ $schedule->expected_harvest_date ? $schedule->expected_harvest_date->format('M j, Y') : 'TBD' }}
                        `
                    },
                    @endforeach
                ].filter(event => event.start !== ''),
                eventDidMount: function(info) {
                    // Add tooltip
                    new bootstrap.Tooltip(info.el, {
                        title: info.event.extendedProps.description || info.event.title,
                        placement: 'top',
                        html: true
                    });
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    
                    // Show modal with event details
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    // Create modal
                    const modalHtml = `
                        <div class="modal-backdrop modal-backdrop-show" style="opacity: 0.5"></div>
                        <div class="modal modal-open" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25); z-index: 1000; max-width: 500px; width: 90%;">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">${event.title}</h3>
                                <button onclick="this.closest('.modal').remove(); this.closest('.modal-backdrop').remove();" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-2">
                                <p><strong>Status:</strong> ${props.status}</p>
                                <p><strong>Field:</strong> ${props.field}</p>
                                @if($schedule->farm)<p><strong>Farm:</strong> ${props.farm}</p>@endif
                                <p><strong>Variety:</strong> ${props.variety}</p>
                                <p><strong>Progress:</strong> ${props.progress}%</p>
                                <p><strong>Expected Harvest:</strong> ${props.harvest}</p>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <a href="{{ url('admin') }}/resources/planting-schedules/${event.id}/edit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Edit Schedule</a>
                            </div>
                        </div>
                    `;
                    
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    
                    // Close on backdrop click
                    document.querySelector('.modal-backdrop').addEventListener('click', function() {
                        document.querySelector('.modal').remove();
                        this.remove();
                    });
                }
            });
            
            calendar.render();
        });
    </script>
    @endpush
</x-filament::page>
