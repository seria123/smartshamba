<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\LivestockType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockController extends Controller
{
    public function index()
    {
        $livestock = Livestock::with('type')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        $types = LivestockType::withCount([
            'livestock as total' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->whereIn('status', ['healthy', 'sick']);
            },
            'livestock as healthy' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('status', 'healthy');
            },
            'livestock as sick' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('status', 'sick');
            },
        ])->get();

        return view('livestock.index', compact('livestock', 'types'));
    }

    public function create()
    {
        $types = LivestockType::all();

        return view('livestock.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name' => 'nullable|string|max:255',
            'tag_number' => 'nullable|string|unique:livestock,tag_number',
            'date_acquired' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $livestock = Livestock::create($validated);

        return redirect()->route('livestock.show', $livestock)
            ->with('success', 'Livestock created successfully');
    }

    public function show(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);

        return view('livestock.show', compact('livestock'));
    }

    public function edit(Livestock $livestock)
    {
        $this->authorizeOwnership($livestock);
        $types = LivestockType::all();

        return view('livestock.edit', compact('livestock', 'types'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        $this->authorize('update', $livestock);

        $validated = $request->validate([
            'livestock_type_id' => 'sometimes|exists:livestock_types,id',
            'name' => 'nullable|string|max:255',
            'tag_number' => 'sometimes|string|unique:livestock,tag_number,'.$livestock->id,
            'date_acquired' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'status' => 'sometimes|in:healthy,sick,sold,dead',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $livestock->update($validated);

        return redirect()->route('livestock.show', $livestock)
            ->with('success', 'Livestock updated successfully');
    }

    public function destroy(Livestock $livestock)
    {
        $this->authorize('delete', $livestock);
        $livestock->delete();

        return redirect()->route('livestock.index')
            ->with('success', 'Livestock deleted successfully');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
