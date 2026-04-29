<?php

namespace App\Http\Controllers;

use App\Models\Fertilizer;
use App\Models\FertilizerType;
use Illuminate\Http\Request;

class FertilizerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fertilizers = Fertilizer::with('fertilizerType')->get();
        return view('fertilizers.index', compact('fertilizers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fertilizerTypes = FertilizerType::where('is_active', true)->get();
        return view('fertilizers.create', compact('fertilizerTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fertilizer_type_id' => 'required|exists:fertilizer_types,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        Fertilizer::create($request->all());

        return redirect()->route('fertilizers.index')
            ->with('success', 'Fertilizer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fertilizer $fertilizer)
    {
        return view('fertilizers.show', compact('fertilizer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fertilizer $fertilizer)
    {
        $fertilizerTypes = FertilizerType::where('is_active', true)->get();
        return view('fertilizers.edit', compact('fertilizer', 'fertilizerTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fertilizer $fertilizer)
    {
        $request->validate([
            'fertilizer_type_id' => 'required|exists:fertilizer_types,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $fertilizer->update($request->all());

        return redirect()->route('fertilizers.index')
            ->with('success', 'Fertilizer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fertilizer $fertilizer)
    {
        $fertilizer->delete();

        return redirect()->route('fertilizers.index')
            ->with('success', 'Fertilizer deleted successfully.');
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
