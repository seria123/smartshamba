<?php

namespace App\Http\Controllers;

use App\Models\FertilizerApplication;
use App\Models\Fertilizer;
use App\Models\Field;
use Illuminate\Http\Request;

class FertilizerApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = FertilizerApplication::with(['fertilizer', 'field'])->get();
        return view('fertilizer_applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fertilizers = Fertilizer::where('is_active', true)->get();
        $fields = Field::with('farm')->get();
        return view('fertilizer_applications.create', compact('fertilizers', 'fields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fertilizer_id' => 'required|exists:fertilizers,id',
            'field_id' => 'required|exists:fields,id',
            'quantity_used' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'application_date' => 'required|date',
            'growth_stage' => 'nullable|string|max:50',
            'application_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        FertilizerApplication::create($request->all());

        return redirect()->route('fertilizer_applications.index')
            ->with('success', 'Fertilizer application recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FertilizerApplication $fertilizerApplication)
    {
        return view('fertilizer_applications.show', compact('fertilizerApplication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FertilizerApplication $fertilizerApplication)
    {
        $fertilizers = Fertilizer::where('is_active', true)->get();
        $fields = Field::with('farm')->get();
        return view('fertilizer_applications.edit', compact('fertilizerApplication', 'fertilizers', 'fields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FertilizerApplication $fertilizerApplication)
    {
        $request->validate([
            'fertilizer_id' => 'required|exists:fertilizers,id',
            'field_id' => 'required|exists:fields,id',
            'quantity_used' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'application_date' => 'required|date',
            'growth_stage' => 'nullable|string|max:50',
            'application_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $fertilizerApplication->update($request->all());

        return redirect()->route('fertilizer_applications.index')
            ->with('success', 'Fertilizer application updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FertilizerApplication $fertilizerApplication)
    {
        $fertilizerApplication->delete();

        return redirect()->route('fertilizer_applications.index')
            ->with('success', 'Fertilizer application deleted successfully.');
    }
}
