<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\Sales\Services\SalesPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesPaymentController extends Controller
{
    public function create(SalesRecord $record): View
    {
        abort_unless($record->status === 'confirmed', 403);
        return view('sales::payments.create', ['record' => $record->load('customer')]);
    }

    public function store(Request $request, SalesRecord $record, SalesPaymentService $service): RedirectResponse
    {
        $service->record($record, $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', Rule::in(['cash', 'mpesa', 'bank_transfer', 'cheque', 'card', 'other'])],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]), $request->user()?->id);

        return redirect()->route('sales.records.show', $record)->with('status', 'Payment recorded.');
    }
}
