<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['farm', 'crop', 'livestock', 'staff']);

        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        if ($request->filled('expense_type')) {
            $type = is_array($request->expense_type) ? $request->expense_type[0] : $request->expense_type;
            $query->whereRaw("FIND_IN_SET(?, expense_type)", [$type]);
        }

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);

        $stats = [
            'total_expenses' => $query->sum('amount'),
            'by_type' => $this->getTypeExpensesSummary(now()->year),
        ];

        return view('expenses.index', compact('expenses', 'stats'));
    }

    public function show(Expense $expense)
    {
        $expense->load(['farm', 'crop', 'staff']);

        return view('expenses.show', compact('expense'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'crop_id' => 'nullable|exists:crops,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'expense_type' => 'required|array',
            'expense_type.*' => 'in:inputs,labor,equipment,fertilizer,seeds,pesticides,animal_feed,veterinary,fuel,maintenance,transport,utilities,other',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'is_recurring' => 'nullable|boolean',
            'recurrence_interval' => 'nullable|in:weekly,monthly,seasonal,yearly',
            'next_due_date' => 'nullable|date',
            'staff_id' => 'nullable|exists:staff,id',
            'notes' => 'nullable|string',
        ]);

        // Convert expense_type array to CSV for storage
        if (is_array($validated['expense_type'])) {
            $validated['expense_type'] = implode(',', $validated['expense_type']);
        }

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts/expenses', 'public');
        }

        $validated['is_recurring'] = $request->boolean('is_recurring');

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'crop_id' => 'nullable|exists:crops,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'expense_type' => 'required|array',
            'expense_type.*' => 'in:inputs,labor,equipment,fertilizer,seeds,pesticides,animal_feed,veterinary,fuel,maintenance,transport,utilities,other',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'is_recurring' => 'nullable|boolean',
            'recurrence_interval' => 'nullable|in:weekly,monthly,seasonal,yearly',
            'next_due_date' => 'nullable|date',
            'staff_id' => 'nullable|exists:staff,id',
            'notes' => 'nullable|string',
        ]);

        // Convert expense_type array to CSV for storage
        if (is_array($validated['expense_type'])) {
            $validated['expense_type'] = implode(',', $validated['expense_type']);
        }

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts/expenses', 'public');
        }

        $validated['is_recurring'] = $request->boolean('is_recurring');

        $expense->update($validated);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }

    public function summary(Request $request)
    {
        $year = $request->filled('year') ? $request->year : now()->year;

        $monthlyExpenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $typeExpenses = $this->getTypeExpensesSummary($year);

        $monthlyRevenue = Revenue::selectRaw('MONTH(sale_date) as month, SUM(amount) as total')
            ->whereYear('sale_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $revenueByCrop = Revenue::selectRaw('crop_id, SUM(amount) as total')
            ->whereYear('sale_date', $year)
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->with('crop:id,name')
            ->get();

        $totalRevenue = Revenue::whereYear('sale_date', $year)->sum('amount');
        $totalExpenses = $typeExpenses->sum('total');
        $netProfit = $totalRevenue - $totalExpenses;

        return view('expenses.summary', compact(
            'monthlyExpenses',
            'typeExpenses',
            'monthlyRevenue',
            'revenueByCrop',
            'year',
            'totalRevenue',
            'totalExpenses',
            'netProfit'
        ));
    }

    /**
     * Calculate expenses grouped by type (handles CSV/array expense_type)
     */
    private function getTypeExpensesSummary($year): Collection
    {
        $expenses = Expense::whereYear('expense_date', $year)->get(['amount', 'expense_type']);
        
        $totals = collect();
        
        foreach ($expenses as $expense) {
            $types = $expense->expense_type; // This uses accessor which returns array
            foreach ($types as $type) {
                $totals->put($type, ($totals->get($type, 0) + $expense->amount));
            }
        }
        
        // Convert to collection of objects matching original structure: { expense_type: X, total: Y }
        return $totals->map(function ($total, $type) {
            return (object) ['expense_type' => $type, 'total' => $total];
        })->values();
    }
}
