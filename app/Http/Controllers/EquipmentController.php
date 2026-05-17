<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Farm;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of equipment.
     */
    public function index()
    {
        $equipment = Equipment::latest()->paginate(10);

        return view('equipment.index', compact('equipment'));
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create()
    {
        $farms = Farm::all();
        return view('equipment.create', compact('farms'));
    }

    /**
     * Store newly created equipment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'required|string',
            'purchase_date' => 'nullable|date',
        ]);

        Equipment::create($validated);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment added successfully ⚙️');
    }

    /**
     * Display a single equipment.
     */
    public function show($id)
    {
        $equipment = Equipment::findOrFail($id);

        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing equipment.
     */
    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $farms = Farm::all();
        return view('equipment.edit', compact('equipment', 'farms'));
    }

    /**
     * Update equipment.
     */
    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'required|string',
            'purchase_date' => 'nullable|date',
        ]);

        $equipment->update($validated);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment updated successfully 🔧');
    }

    /**
     * Remove equipment.
     */
    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment deleted 🗑️');
    }
}