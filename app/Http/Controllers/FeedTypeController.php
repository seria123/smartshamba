<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FeedType;
use App\Models\Livestock;
use App\Models\Supplier;
use Illuminate\Http\Request;

class FeedTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = FeedType::with(['linkedCrop', 'linkedLivestock', 'supplier', 'foodStocks']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('demand_level')) {
            $query->where('demand_level', $request->demand_level);
        }

        $feedTypes = $query->get()->sortByDesc(fn (FeedType $feedType) => $feedType->profitMargin());

        $stats = [
            'total' => $feedTypes->count(),
            'low_stock' => $feedTypes->filter->isBelowThreshold()->count(),
            'price_alerts' => $feedTypes->filter->priceBelowCost()->count(),
            'expiry_alerts' => $feedTypes->filter(fn ($feedType) => $feedType->expiryAlert())->count(),
        ];

        return view('feed-types.index', compact('feedTypes', 'stats'));
    }

    public function create()
    {
        return view('feed-types.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:feed_types,name',
        ] + $this->rules());

        $validated = $this->preparePayload($request, $validated);

        FeedType::create($validated);

        return redirect()->route('feed-types.index')
            ->with('success', 'Feed type created successfully');
    }

    public function show(FeedType $feedType)
    {
        $feedType->load(['linkedCrop', 'linkedLivestock', 'supplier', 'foodStocks.supplier']);

        return view('feed-types.show', compact('feedType'));
    }

    public function edit(FeedType $feedType)
    {
        return view('feed-types.edit', ['feedType' => $feedType] + $this->formData());
    }

    public function update(Request $request, FeedType $feedType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:feed_types,name,'.$feedType->id,
        ] + $this->rules());

        $validated = $this->preparePayload($request, $validated, $feedType);

        $feedType->update($validated);

        return redirect()->route('feed-types.index')
            ->with('success', 'Feed type updated successfully');
    }

    public function destroy(FeedType $feedType)
    {
        $feedType->delete();

        return redirect()->route('feed-types.index')
            ->with('success', 'Feed type deleted successfully');
    }

    private function formData(): array
    {
        return [
            'crops' => Crop::orderBy('name')->get(),
            'livestock' => Livestock::orderBy('tag_number')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'categories' => [
                'crop_based' => 'Crop-based',
                'animal_based' => 'Animal-based',
                'processed' => 'Processed',
            ],
            'subCategories' => ['Dairy', 'Grains', 'Vegetables', 'Meat', 'Eggs', 'Fruits', 'Feeds', 'Other'],
            'units' => ['kg', 'litres', 'pieces', 'bags', 'tonnes', 'crates'],
            'storageOptions' => ['refrigerated', 'dry_storage', 'frozen', 'cool_dark_place', 'airtight'],
            'certificationOptions' => ['organic', 'export_quality', 'kebs', 'fair_trade', 'grade_a'],
            'demandLevels' => ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'],
            'qualityGrades' => ['A' => 'Grade A', 'B' => 'Grade B', 'C' => 'Grade C'],
        ];
    }

    private function rules(): array
    {
        return [
            'category' => 'required|in:crop_based,animal_based,processed',
            'sub_category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'default_unit' => 'required|string|max:50',
            'unit_conversion_label' => 'nullable|string|max:100',
            'unit_conversion_factor' => 'nullable|numeric|min:0',
            'min_threshold' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'minimum_price' => 'nullable|numeric|min:0',
            'market_price' => 'nullable|numeric|min:0',
            'linked_crop_id' => 'nullable|exists:crops,id',
            'linked_livestock_id' => 'nullable|exists:livestock,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'protein' => 'nullable|numeric|min:0',
            'carbohydrates' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'energy_calories' => 'nullable|numeric|min:0',
            'vitamins_text' => 'nullable|string',
            'expiry_period_days' => 'nullable|integer|min:0',
            'storage_conditions' => 'nullable|array',
            'raw_input_name' => 'nullable|string|max:255',
            'processed_output_name' => 'nullable|string|max:255',
            'processing_cost' => 'nullable|numeric|min:0',
            'yield_ratio' => 'nullable|numeric|min:0',
            'input_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'batch_number' => 'nullable|string|max:255',
            'production_date' => 'nullable|date',
            'source_batch' => 'nullable|string|max:255',
            'demand_level' => 'nullable|in:high,medium,low',
            'best_selling_periods_text' => 'nullable|string',
            'buyer_preferences' => 'nullable|string',
            'market_regions_text' => 'nullable|string',
            'region_pricing_text' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'quality_grade' => 'nullable|in:A,B,C',
            'certifications' => 'nullable|array',
            'user_notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function preparePayload(Request $request, array $validated, ?FeedType $feedType = null): array
    {
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['vitamins'] = $this->listFromText($request->input('vitamins_text'));
        $validated['best_selling_periods'] = $this->listFromText($request->input('best_selling_periods_text'));
        $validated['market_regions'] = $this->listFromText($request->input('market_regions_text'));
        $validated['region_pricing'] = $this->listFromText($request->input('region_pricing_text'));
        $validated['price_history'] = $this->priceHistory($feedType, $validated);

        unset(
            $validated['vitamins_text'],
            $validated['best_selling_periods_text'],
            $validated['market_regions_text'],
            $validated['region_pricing_text'],
            $validated['image']
        );

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('feed-types', 'public');
        }

        return $validated;
    }

    private function listFromText(?string $text): array
    {
        return collect(explode(',', (string) $text))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function priceHistory(?FeedType $feedType, array $validated): array
    {
        $history = $feedType?->price_history ?? [];
        $price = $validated['market_price'] ?? $validated['selling_price'] ?? null;

        if ($price !== null && (! $feedType || (float) $feedType->market_price !== (float) $price)) {
            $history[] = [
                'date' => now()->toDateString(),
                'market_price' => $validated['market_price'] ?? null,
                'selling_price' => $validated['selling_price'] ?? null,
            ];
        }

        return array_slice($history, -20);
    }
}
