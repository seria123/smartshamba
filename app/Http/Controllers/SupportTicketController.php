<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with('user')->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('support-tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        $categories = [
            'technical' => 'Technical Issue',
            'billing' => 'Billing & Payments',
            'account' => 'Account & Profile',
            'farm_management' => 'Farm Management',
            'livestock' => 'Livestock',
            'crop' => 'Crop & Field',
            'feature_request' => 'Feature Request',
            'other' => 'Other',
        ];

        $priorities = [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        return view('support-tickets.create', compact('categories', 'priorities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,normal,high,urgent',
            'category' => 'required|in:technical,billing,account,farm_management,livestock,crop,feature_request,other',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'open';

        SupportTicket::create($validated);

        return redirect()->route('support-tickets.index')
            ->with('success', 'Support ticket created successfully.');
    }

    public function show(SupportTicket $supportTicket): View
    {
        if ($supportTicket->user_id !== auth()->id()) {
            abort(403, 'You do not have access to this ticket.');
        }

        $supportTicket->load('user');

        return view('support-tickets.show', compact('supportTicket'));
    }
}
