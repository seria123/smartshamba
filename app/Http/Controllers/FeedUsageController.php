<?php

namespace App\Http\Controllers;

use App\Models\FeedUsage;
use App\Models\FeedType;
use App\Models\Livestock;
use Illuminate\Http\Request;

class FeedUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedUsages = FeedUsage::with(['feedType', 'livestock'])->get();
        return view('feed_usages.index', compact('feedUsages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $feedTypes = FeedType::where('is_active', true)->get();
        $livestocks = Livestock::whereIn('status', ['healthy', 'sick'])->get(); // Only active livestock
        return view('feed_usages.create', compact('feedTypes', 'livestocks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'feed_type_id' => 'required|exists:feed_types,id',
            'livestock_id' => 'required|exists:livestock,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'usage_date' => 'required|date',
            'usage_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        FeedUsage::create($request->all());

        return redirect()->route('feed_usages.index')
            ->with('success', 'Feed usage recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeedUsage $feedUsage)
    {
        return view('feed_usages.show', compact('feedUsage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeedUsage $feedUsage)
    {
        $feedTypes = FeedType::where('is_active', true)->get();
        $livestocks = Livestock::whereIn('status', ['healthy', 'sick'])->get();
        return view('feed_usages.edit', compact('feedUsage', 'feedTypes', 'livestocks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeedUsage $feedUsage)
    {
        $request->validate([
            'feed_type_id' => 'required|exists:feed_types,id',
            'livestock_id' => 'required|exists:livestock,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'usage_date' => 'required|date',
            'usage_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $feedUsage->update($request->all());

        return redirect()->route('feed_usages.index')
            ->with('success', 'Feed usage updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeedUsage $feedUsage)
    {
        $feedUsage->delete();

        return redirect()->route('feed_usages.index')
            ->with('success', 'Feed usage deleted successfully.');
    }
}
