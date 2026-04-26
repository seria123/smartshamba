<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['farm', 'crop', 'worker']);

        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
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
            'by_type' => Expense::selectRaw('expense_type, SUM(amount) as total')
                ->groupBy('expense_type')
                ->pluck('total', 'expense_type'),
        ];

        return view('expenses.index', compact('expenses', 'stats'));
    }

    public function show(Expense $expense)
    {
        $expense->load(['farm', 'crop', 'worker']);

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
            'expense_type' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'worker_id' => 'nullable|exists:workers,id',
            'notes' => 'nullable|string',
        ]);

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
            'expense_type' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'worker_id' => 'nullable|exists:workers,id',
            'notes' => 'nullable|string',
        ]);

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

        $typeExpenses = Expense::selectRaw('expense_type, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupBy('expense_type')
            ->get();

        return view('expenses.summary', compact('monthlyExpenses', 'typeExpenses', 'year'));
    }
}
