<?php

namespace App\Http\Controllers;

use App\Models\CropCycle;
use App\Models\Farm;
use App\Models\Livestock;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['farm', 'cropCycle', 'livestock'])->latest()->paginate(15);

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        return view('loans.create', $this->formData());
    }

    public function store(Request $request)
    {
        $loan = new Loan($this->preparePayload($request, $this->validated($request)));
        $loan->repayment_schedule = $loan->generateSchedule();
        $risk = $loan->riskSummary();
        $loan->risk_level = $risk['level'];
        $loan->risk_notes = $risk['notes'];
        $loan->interest_saved_estimate = $loan->earlyRepaymentSavings();
        $loan->save();

        return redirect()->route('loans.index')->with('success', 'Loan recorded successfully.');
    }

    public function edit(Loan $loan)
    {
        return view('loans.edit', ['loan' => $loan] + $this->formData());
    }

    public function update(Request $request, Loan $loan)
    {
        $loan->fill($this->preparePayload($request, $this->validated($request), $loan));
        $loan->repayment_schedule = $loan->generateSchedule();
        $risk = $loan->riskSummary();
        $loan->risk_level = $risk['level'];
        $loan->risk_notes = $risk['notes'];
        $loan->interest_saved_estimate = $loan->earlyRepaymentSavings();
        $loan->save();

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'loan_name' => 'nullable|string|max:255',
            'purpose_tag' => 'nullable|in:crop_input,livestock,equipment,working_capital,other',
            'lender_name' => 'required|string|max:255',
            'lender_type' => 'nullable|in:bank,sacco,individual,cooperative,other',
            'lender_contact' => 'nullable|string|max:255',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'duration_months' => 'nullable|integer|min:1|max:360',
            'interest_method' => 'required|in:flat,reducing_balance,compound',
            'amount_repaid' => 'required|numeric|min:0',
            'loan_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:loan_date',
            'repayment_frequency' => 'nullable|in:weekly,monthly,seasonal,annual,one_time',
            'payment_method' => 'nullable|in:mpesa,cash,bank,cheque,other',
            'payments_text' => 'nullable|string',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'allocation_inputs' => 'nullable|numeric|min:0',
            'allocation_labor' => 'nullable|numeric|min:0',
            'allocation_equipment' => 'nullable|numeric|min:0',
            'expected_profit' => 'nullable|numeric|min:0',
            'generated_profit' => 'nullable|numeric|min:0',
            'farm_income_snapshot' => 'nullable|numeric|min:0',
            'early_repayment_extra' => 'nullable|numeric|min:0',
            'comparison_notes' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:8192',
            'late_penalty_rate' => 'nullable|numeric|min:0|max:100',
            'extra_charges' => 'nullable|numeric|min:0',
            'group_loan_members_text' => 'nullable|string',
            'status' => 'required|in:active,paid,defaulted,restructured',
            'notes' => 'nullable|string',
        ]);
    }

    private function formData(): array
    {
        return [
            'farms' => Farm::orderBy('name')->get(),
            'cropCycles' => CropCycle::orderByDesc('created_at')->get(),
            'livestock' => Livestock::orderBy('tag_number')->get(),
        ];
    }

    private function preparePayload(Request $request, array $validated, ?Loan $loan = null): array
    {
        $validated['duration_months'] = $validated['duration_months'] ?? 12;
        $validated['allocation_inputs'] = $validated['allocation_inputs'] ?? 0;
        $validated['allocation_labor'] = $validated['allocation_labor'] ?? 0;
        $validated['allocation_equipment'] = $validated['allocation_equipment'] ?? 0;
        $validated['late_penalty_rate'] = $validated['late_penalty_rate'] ?? 0;
        $validated['extra_charges'] = $validated['extra_charges'] ?? 0;
        $validated['payments'] = $this->parsePayments($request->input('payments_text'));
        $validated['group_loan_members'] = $this->parseLines($request->input('group_loan_members_text'));
        $validated['document_paths'] = $loan?->document_paths ?? [];

        if ($request->hasFile('documents')) {
            $validated['document_paths'] = array_merge(
                $validated['document_paths'],
                collect($request->file('documents'))
                    ->map(fn ($file) => $file->store('loan-documents', 'public'))
                    ->values()
                    ->all()
            );
        }

        $validated['remaining_principal'] = max(0, (float) $validated['principal_amount'] - (float) $validated['amount_repaid']);

        unset($validated['payments_text'], $validated['group_loan_members_text'], $validated['documents']);

        return $validated;
    }

    private function parsePayments(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(function ($line) {
                $parts = array_map('trim', explode('|', $line));

                return count($parts) >= 2 ? [
                    'date' => $parts[0],
                    'amount' => (float) $parts[1],
                    'method' => $parts[2] ?? null,
                    'reference' => $parts[3] ?? null,
                ] : null;
            })
            ->filter()
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
