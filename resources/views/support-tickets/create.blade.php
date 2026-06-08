@extends('layouts.MainLayout')

@section('title', 'New Support Ticket - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">New Support Ticket</h1>
        <a href="{{ route('support-tickets.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i> Back to Tickets
        </a>
    </div>

    <x-ui.card>
        <form action="{{ route('support-tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Brief description of your issue" required>
                @error('subject')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">Select Priority</option>
                        @foreach($priorities as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', 'medium') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Channel</label>
                    <select name="support_channel" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                        @foreach($channels as $value => $label)
                            <option value="{{ $value }}" {{ old('support_channel', 'in_app') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                    <select name="assigned_role" id="assigned_role" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Smart routing</option>
                        @foreach($agentRoles as $value => $label)
                            <option value="{{ $value }}" {{ old('assigned_role') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea name="message" rows="6"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                          placeholder="Describe your issue in detail..." required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm font-semibold text-amber-900"><i class="fas fa-lightbulb mr-2"></i>AI Hint System</p>
                <p id="ai-hint" class="mt-2 text-sm text-amber-800">Describe the issue and SmartShamba will suggest likely fixes before you submit.</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Photos / Videos / Receipts</label>
                <input type="file" name="media[]" multiple accept=".jpg,.jpeg,.png,.webp,.mp4,.mov,.pdf"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                <p class="text-xs text-gray-500 mt-1">Upload crop/livestock photos, short videos, screenshots, or payment receipts.</p>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('support-tickets.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Ticket</span>
                </button>
            </div>
        </form>
    </x-ui.card>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const message = document.querySelector('textarea[name="message"]');
    const category = document.getElementById('category');
    const assignedRole = document.getElementById('assigned_role');
    const hint = document.getElementById('ai-hint');

    const hints = {
        crop: 'Check soil moisture, recent fertilizer use, pests, and upload a clear leaf or whole-plant photo.',
        livestock: 'Mention animal age, symptoms, vaccination history, and upload photos or a short video.',
        finance: 'Attach invoice numbers, M-Pesa messages, buyer names, and payment dates.',
        system_bug: 'Include the page name, error message, screenshot, and what you clicked before the issue.',
        farm_management: 'Mention farm, field, activity, staff, or weather context involved.'
    };

    function updateHint() {
        const text = (message.value || '').toLowerCase();
        const selected = category.value;
        hint.textContent = hints[selected] || 'Describe the issue and SmartShamba will suggest likely fixes before you submit.';

        if (!assignedRole.value) {
            if (selected === 'crop' || text.includes('yellow') || text.includes('leaf')) assignedRole.value = 'agronomist';
            if (selected === 'livestock' || text.includes('cow') || text.includes('chicken')) assignedRole.value = 'vet';
            if (selected === 'system_bug' || text.includes('login') || text.includes('error')) assignedRole.value = 'technical_support';
        }
    }

    message.addEventListener('input', updateHint);
    category.addEventListener('change', updateHint);
    updateHint();
});
</script>
@endsection
