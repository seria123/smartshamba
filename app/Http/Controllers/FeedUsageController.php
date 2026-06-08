<?php

namespace App\Http\Controllers;

use App\Models\FeedUsage;
use App\Models\FeedType;
use App\Models\FoodStock;
use App\Models\Livestock;
use App\Models\Staff;
use App\Models\Supplier;
use Illuminate\Http\Request;

class FeedUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FeedUsage::with(['feedType', 'livestock', 'staff', 'supplier'])->latest('usage_date');

        if ($request->filled('feed_type_id')) {
            $query->where('feed_type_id', $request->feed_type_id);
        }

        if ($request->filled('feeding_frequency')) {
            $query->where('feeding_frequency', $request->feeding_frequency);
        }

        if ($request->filled('anomaly_status')) {
            $query->where('anomaly_status', $request->anomaly_status);
        }

        $feedUsages = $query->paginate(15);
        $feedTypes = FeedType::where('is_active', true)->orderBy('name')->get();

        $monthlyCost = FeedUsage::whereMonth('usage_date', now()->month)
            ->whereYear('usage_date', now()->year)
            ->sum('total_cost');

        $weeklyUsage = FeedUsage::where('usage_date', '>=', now()->subDays(7))->sum('quantity');
        $previousWeeklyUsage = FeedUsage::whereBetween('usage_date', [now()->subDays(14), now()->subDays(8)])->sum('quantity');
        $weeklyTrend = $previousWeeklyUsage > 0 ? (($weeklyUsage - $previousWeeklyUsage) / $previousWeeklyUsage) * 100 : null;

        $stats = [
            'monthly_cost' => $monthlyCost,
            'weekly_usage' => $weeklyUsage,
            'weekly_trend' => $weeklyTrend,
            'anomalies' => FeedUsage::whereNotNull('anomaly_status')->where('anomaly_status', '!=', 'normal')->count(),
            'reminders' => FeedUsage::whereNotNull('reminder_at')->where('reminder_at', '>=', now())->count(),
        ];

        return view('feed_usages.index', compact('feedUsages', 'feedTypes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $feedTypes = FeedType::where('is_active', true)->get();
        $livestocks = Livestock::whereIn('status', ['healthy', 'sick'])->get(); // Only active livestock
        $staff = Staff::where('status', 'active')->orderBy('first_name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('feed_usages.create', compact('feedTypes', 'livestocks', 'staff', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated = $this->preparePayload($request, $validated);
        $validated = $this->deductInventory($validated);

        FeedUsage::create($validated);

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
        $staff = Staff::where('status', 'active')->orderBy('first_name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('feed_usages.edit', compact('feedUsage', 'feedTypes', 'livestocks', 'staff', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeedUsage $feedUsage)
    {
        $validated = $this->validated($request);
        $validated = $this->preparePayload($request, $validated, $feedUsage);

        if (! $feedUsage->inventory_deducted) {
            $validated = $this->deductInventory($validated);
        }

        $feedUsage->update($validated);

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

    private function validated(Request $request): array
    {
        return $request->validate([
            'feed_type_id' => 'required|exists:feed_types,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'group_name' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'feeding_frequency' => 'required|in:daily,weekly,custom',
            'feeding_time' => 'nullable|in:morning,midday,evening,night,custom',
            'reminder_at' => 'nullable|date',
            'unit_conversion_factor' => 'nullable|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'usage_date' => 'required|date',
            'usage_type' => 'nullable|string|max:50',
            'output_type' => 'nullable|in:milk,eggs,weight_gain,none',
            'output_quantity' => 'nullable|numeric|min:0',
            'output_unit' => 'nullable|string|max:50',
            'weight_gain' => 'nullable|numeric|min:0',
            'deduct_inventory' => 'nullable|boolean',
            'season' => 'nullable|in:dry,rainy,cold,hot',
            'production_stage' => 'nullable|string|max:255',
            'target_protein' => 'nullable|numeric|min:0',
            'target_energy' => 'nullable|numeric|min:0',
            'minerals_text' => 'nullable|string',
            'staff_id' => 'nullable|exists:staff,id',
            'quality_image' => 'nullable|image|max:4096',
            'quality_notes' => 'nullable|string',
            'spoilage_status' => 'nullable|in:none,mold,spoilage,poor_quality',
            'batch_number' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);
    }

    private function preparePayload(Request $request, array $validated, ?FeedUsage $feedUsage = null): array
    {
        $validated['deduct_inventory'] = $request->boolean('deduct_inventory', true);
        $validated['unit_conversion_factor'] = $validated['unit_conversion_factor'] ?? 1;
        $validated['unit_cost'] = $validated['unit_cost'] ?? FeedType::find($validated['feed_type_id'])?->averageUnitCost();
        $validated['total_cost'] = (float) $validated['quantity'] * (float) ($validated['unit_cost'] ?: 0);
        $validated['feed_conversion_ratio'] = $this->calculateFcr($validated);
        $validated['trend_change_percent'] = $this->trendChange($validated);
        $validated['anomaly_status'] = $this->anomalyStatus($validated);
        $validated['ai_recommendation'] = $this->recommendation($validated);
        $validated['minerals'] = $this->listFromText($request->input('minerals_text'));

        unset($validated['minerals_text'], $validated['quality_image']);

        if ($request->hasFile('quality_image')) {
            $validated['quality_image_path'] = $request->file('quality_image')->store('feed-quality', 'public');
        } elseif ($feedUsage?->quality_image_path) {
            $validated['quality_image_path'] = $feedUsage->quality_image_path;
        }

        return $validated;
    }

    private function deductInventory(array $validated): array
    {
        if (! ($validated['deduct_inventory'] ?? false)) {
            return $validated;
        }

        $remaining = (float) $validated['quantity'] * (float) ($validated['unit_conversion_factor'] ?: 1);
        $validated['stock_before'] = FeedType::find($validated['feed_type_id'])?->totalQuantity() ?? 0;

        FoodStock::where('feed_type_id', $validated['feed_type_id'])
            ->where('is_active', true)
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
            ->get()
            ->each(function (FoodStock $stock) use (&$remaining) {
                if ($remaining <= 0) {
                    return;
                }

                $deduct = min((float) $stock->quantity, $remaining);
                $stock->quantity = (float) $stock->quantity - $deduct;
                $stock->is_active = $stock->quantity > 0;
                $stock->save();
                $remaining -= $deduct;
            });

        $validated['stock_after'] = FeedType::find($validated['feed_type_id'])?->totalQuantity() ?? 0;
        $validated['inventory_deducted'] = true;

        return $validated;
    }

    private function calculateFcr(array $validated): ?float
    {
        $feed = (float) $validated['quantity'] * (float) ($validated['unit_conversion_factor'] ?: 1);
        $output = (float) ($validated['weight_gain'] ?? $validated['output_quantity'] ?? 0);

        return $output > 0 ? round($feed / $output, 4) : null;
    }

    private function trendChange(array $validated): ?float
    {
        $previous = FeedUsage::where('feed_type_id', $validated['feed_type_id'])
            ->where('usage_date', '<', $validated['usage_date'])
            ->latest('usage_date')
            ->value('quantity');

        return $previous > 0 ? round((((float) $validated['quantity'] - (float) $previous) / (float) $previous) * 100, 2) : null;
    }

    private function anomalyStatus(array $validated): string
    {
        $trend = $this->trendChange($validated);

        if ($trend !== null && $trend >= 30) {
            return 'spike';
        }

        if ($trend !== null && $trend <= -30) {
            return 'drop';
        }

        if (($validated['spoilage_status'] ?? 'none') !== 'none') {
            return 'quality_issue';
        }

        return 'normal';
    }

    private function recommendation(array $validated): string
    {
        $feedType = FeedType::find($validated['feed_type_id']);
        $fcr = $this->calculateFcr($validated);
        $messages = [];

        if (($validated['anomaly_status'] ?? $this->anomalyStatus($validated)) === 'spike') {
            $messages[] = 'Feed use rose sharply. Check for overfeeding, wastage, stress, or poor feed quality.';
        }

        if (($validated['anomaly_status'] ?? $this->anomalyStatus($validated)) === 'drop') {
            $messages[] = 'Feed intake dropped sharply. Watch for illness, heat stress, or appetite changes.';
        }

        if ($fcr && $fcr > 3) {
            $messages[] = 'FCR is high. Review ration quality and output performance.';
        }

        if ($feedType?->protein && isset($validated['target_protein']) && $validated['target_protein'] && $feedType->protein < $validated['target_protein']) {
            $messages[] = 'Protein may be below target. Consider supplementing or mixing with higher-protein feed.';
        }

        if (($validated['season'] ?? null) === 'dry') {
            $messages[] = 'Dry season feeding may need more roughage planning and water availability checks.';
        }

        return $messages ? implode(' ', $messages) : 'Feeding record looks normal. Keep tracking output to improve feed optimization.';
    }

    private function listFromText(?string $text): array
    {
        return collect(explode(',', (string) $text))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
