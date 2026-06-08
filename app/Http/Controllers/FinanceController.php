<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\FoodStock;
use App\Models\Loan;
use App\Models\Revenue;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function dashboard(Request $request)
    {
        $year = (int) $request->input('year', now()->year);

        $totalIncome = Revenue::whereYear('sale_date', $year)->sum('amount');
        $totalExpenses = Expense::whereYear('expense_date', $year)->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $incomeByType = Revenue::selectRaw('income_type, SUM(amount) as total')
            ->whereYear('sale_date', $year)
            ->groupBy('income_type')
            ->orderByDesc('total')
            ->get();

        $expenseByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $cropProfitability = Revenue::selectRaw('crop_id, SUM(amount) as income')
            ->whereYear('sale_date', $year)
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->with('crop:id,name')
            ->get()
            ->map(function ($row) use ($year) {
                $expenses = Expense::where('crop_id', $row->crop_id)
                    ->whereYear('expense_date', $year)
                    ->sum('amount');

                $row->expenses = $expenses;
                $row->profit = $row->income - $expenses;

                return $row;
            });

        $livestockProfitability = Revenue::selectRaw('livestock_id, SUM(amount) as income')
            ->whereYear('sale_date', $year)
            ->whereNotNull('livestock_id')
            ->groupBy('livestock_id')
            ->with('livestock:id,tag_number,name')
            ->get()
            ->map(function ($row) use ($year) {
                $expenses = Expense::where('livestock_id', $row->livestock_id)
                    ->whereYear('expense_date', $year)
                    ->sum('amount');

                $row->expenses = $expenses;
                $row->profit = $row->income - $expenses;

                return $row;
            });

        $budgets = Budget::with(['farm', 'crop', 'livestock'])
            ->whereYear('start_date', '<=', $year)
            ->whereYear('end_date', '>=', $year)
            ->latest()
            ->take(6)
            ->get();

        $loans = Loan::with('farm')
            ->where('status', 'active')
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->take(6)
            ->get();

        $inventoryValue = FoodStock::sum(\Illuminate\Support\Facades\DB::raw('quantity * unit_cost'));
        $recurringExpenses = Expense::where('is_recurring', true)->orderBy('next_due_date')->take(5)->get();

        $insights = $this->buildInsights($netProfit, $totalIncome, $totalExpenses, $cropProfitability, $livestockProfitability, $budgets, $loans);

        return view('finance.dashboard', compact(
            'year',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'incomeByType',
            'expenseByCategory',
            'cropProfitability',
            'livestockProfitability',
            'budgets',
            'loans',
            'inventoryValue',
            'recurringExpenses',
            'insights'
        ));
    }

    private function buildInsights($netProfit, $totalIncome, $totalExpenses, $cropProfitability, $livestockProfitability, $budgets, $loans): array
    {
        $insights = [];

        if ($totalIncome > 0 && ($totalExpenses / $totalIncome) > 0.85) {
            $insights[] = 'Expenses are using more than 85% of farm income. Review high-cost categories before the next buying cycle.';
        }

        if ($netProfit < 0) {
            $insights[] = 'The farm is currently operating at a loss for the selected year.';
        }

        $weakCrop = $cropProfitability->sortBy('profit')->first();
        if ($weakCrop && $weakCrop->profit < 0) {
            $insights[] = ($weakCrop->crop?->name ?? 'A crop') . ' is costing more than it earns.';
        }

        $bestLivestock = $livestockProfitability->sortByDesc('profit')->first();
        if ($bestLivestock && $bestLivestock->profit > 0) {
            $name = $bestLivestock->livestock?->name ?: $bestLivestock->livestock?->tag_number;
            $insights[] = ($name ?: 'A livestock unit') . ' is your strongest livestock profit contributor.';
        }

        foreach ($budgets as $budget) {
            if ($budget->isExceeded()) {
                $insights[] = $budget->name . ' has exceeded its planned budget.';
                break;
            }
        }

        foreach ($loans as $loan) {
            if ($loan->isDueSoon()) {
                $insights[] = 'Loan repayment to ' . $loan->lender_name . ' is due within 3 days.';
                break;
            }
        }

        return $insights ?: ['Finance tracking is ready. Add income, expenses, budgets, and loans to unlock sharper insights.'];
    }
}
