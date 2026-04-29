@extends('layouts.MainLayout')
@section('title', 'Crop Cycles - SmartShamba')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Crop Cycles
                        </h1>
                        <a href="{{ route('crop_cycles.create') }}" class="btn btn-primary">
                            Add New Crop Cycle
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($cropCycles->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        <p>No crop cycles found. <a href="{{ route('crop_cycles.create') }}">Add your first crop cycle</a>.</p>
                    </div>
                @else
                    <div class="p-6 space-y-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Field
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Crop
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Start Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Current Stage
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Expected Harvest
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($cropCycles as $cropCycle)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $cropCycle->field->name ?? 'Unknown Field' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $cropCycle->crop->name ?? 'Unknown Crop' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $cropCycle->start_date ? $cropCycle->start_date->format('M d, Y') : 'Not set' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        bg-green-100 text-green-800">
                                                    {{ $cropCycle->current_stage ?? 'Not analyzed' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $cropCycle->expected_harvest_date ? $cropCycle->expected_harvest_date->format('M d, Y') : 'Not set' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('crop_cycles.show', $cropCycle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    View
                                                </a>
                                                |
                                                <a href="{{ route('crop_cycles.edit', $cropCycle) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    Edit
                                                </a>
                                                |
                                                <form action="{{ route('crop_cycles.destroy', $cropCycle) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this crop cycle?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                     </div>
                 @endif
             </div>
         </div>
     </div>
@endsection