<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Crop;
use App\Models\CropCycle;
use App\Models\Farm;
use App\Models\Livestock;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with(['farm', 'crop', 'cropCycle', 'livestock', 'loan', 'owner'])->latest()->paginate(15);

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        return view('budgets.create', $this->formData());
    }

    public function store(Request $request)
    {
        Budget::create($this->preparePayload($request, $this->validated($request)));

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget)
    {
        return view('budgets.edit', ['budget' => $budget] + $this->formData());
    }

    public function update(Request $request, Budget $budget)
    {
        $budget->update($this->preparePayload($request, $this->validated($request), $budget));

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'crop_id' => 'nullable|exists:crops,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'loan_id' => 'nullable|exists:loans,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'owner_group_name' => 'nullable|string|max:255',
            'responsible_role' => 'nullable|in:manager,worker,agronomist,vet,finance,owner',
            'name' => 'required|string|max:255',
            'season_name' => 'nullable|string|max:255',
            'budget_type' => 'required|in:seasonal,monthly,annual,project,crop,livestock',
            'category' => 'nullable|string|max:255',
            'line_items_text' => 'nullable|string',
            'planned_amount' => 'required|numeric|min:0',
            'actual_amount_override' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'alert_threshold_percent' => 'required|numeric|min:1|max:200',
            'allow_overspend' => 'nullable|boolean',
            'alert_thresholds_text' => 'nullable|string|max:255',
            'forecast_amount' => 'nullable|numeric|min:0',
            'expected_income' => 'nullable|numeric|min:0',
            'generated_profit' => 'nullable|numeric|min:0',
            'market_price_assumption' => 'nullable|numeric|min:0',
            'what_if_scenarios_text' => 'nullable|string',
            'ai_suggestions' => 'nullable|string',
            'cost_efficiency_notes' => 'nullable|string',
            'approval_status' => 'required|in:draft,review,approved,rejected,adjusted',
            'approved_by' => 'nullable|exists:users,id',
            'adjustment_log_text' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:8192',
            'inventory_link_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
    }

    private function formData(): array
    {
        return [
            'farms' => Farm::orderBy('name')->get(),
            'crops' => Crop::orderBy('name')->get(),
            'cropCycles' => CropCycle::orderByDesc('created_at')->get(),
            'livestock' => Livestock::orderBy('tag_number')->get(),
            'loans' => Loan::orderByDesc('created_at')->get(),
            'users' => User::orderBy('name')->get(),
        ];
    }

    private function preparePayload(Request $request, array $validated, ?Budget $budget = null): array
    {
        $validated['allow_overspend'] = $request->boolean('allow_overspend');
        $validated['line_items'] = $this->parseLineItems($request->input('line_items_text'));
        $validated['alert_thresholds'] = $this->parseThresholds($request->input('alert_thresholds_text'));
        $validated['what_if_scenarios'] = $this->parseLines($request->input('what_if_scenarios_text'));
        $validated['adjustment_log'] = $this->parseLines($request->input('adjustment_log_text'));
        $validated['document_paths'] = $budget?->document_paths ?? [];

        if (($validated['approval_status'] ?? null) === 'approved' && empty($budget?->approved_at)) {
            $validated['approved_at'] = now();
        }

        if ($request->hasFile('documents')) {
            $validated['document_paths'] = array_merge(
                $validated['document_paths'],
                collect($request->file('documents'))
                    ->map(fn ($file) => $file->store('budget-documents', 'public'))
                    ->values()
                    ->all()
            );
        }

        unset(
            $validated['line_items_text'],
            $validated['alert_thresholds_text'],
            $validated['what_if_scenarios_text'],
            $validated['adjustment_log_text'],
            $validated['documents']
        );

        return $validated;
    }

    private function parseLineItems(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(function ($line) {
                $parts = array_map('trim', explode('|', $line));

                return count($parts) >= 2 ? [
                    'category' => $parts[0],
                    'planned' => (float) $parts[1],
                    'owner' => $parts[2] ?? null,
                    'notes' => $parts[3] ?? null,
                ] : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function parseThresholds(?string $text): array
    {
        $thresholds = $text ?: '50,80,100';

        return collect(explode(',', $thresholds))
            ->map(fn ($threshold) => trim($threshold))
            ->filter(fn ($threshold) => is_numeric($threshold))
            ->map(fn ($threshold) => (float) $threshold)
            ->values()
            ->all();
    }

    private function parseLines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
