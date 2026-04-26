<?php

namespace App\Http\Controllers;

use App\Models\LivestockType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LivestockTypeController extends Controller
{
    public function index()
    {
        $types = LivestockType::withCount([
            'livestock as total_count' => function ($query) {
                $query->whereIn('status', ['healthy', 'sick']);
            },
            'livestock as healthy_count' => function ($query) {
                $query->where('status', 'healthy');
            },
            'livestock as sick_count' => function ($query) {
                $query->where('status', 'sick');
            },
        ])->get();

        return view('livestock.types.index', compact('types'));
    }

    public function create()
    {
        return view('livestock.types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:livestock_types,slug',
            'description' => 'nullable|string',
            'requires_individual_tracking' => 'boolean',
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Ensure slug uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (LivestockType::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug.'-'.$counter++;
            }
        }

        LivestockType::create($validated);

        return redirect()->route('livestock-types.index')
            ->with('success', 'Livestock type created successfully');
    }

    public function show(LivestockType $livestockType)
    {
        $livestockType->load(['livestock' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('livestock.types.show', compact('livestockType'));
    }

    public function edit(LivestockType $livestockType)
    {
        return view('livestock.types.edit', compact('livestockType'));
    }

    public function update(Request $request, LivestockType $livestockType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:livestock_types,slug,'.$livestockType->id,
            'description' => 'nullable|string',
            'requires_individual_tracking' => 'boolean',
        ]);

        $livestockType->update($validated);

        return redirect()->route('livestock-types.show', $livestockType)
            ->with('success', 'Livestock type updated successfully');
    }

    public function destroy(LivestockType $livestockType)
    {
        if ($livestockType->livestock()->count() > 0) {
            return redirect()->route('livestock-types.index')
                ->with('error', 'Cannot delete livestock type with associated livestock');
        }

        $livestockType->delete();

        return redirect()->route('livestock-types.index')
            ->with('success', 'Livestock type deleted successfully');
    }
}
