<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Http\Controllers\Concerns\PreparesNotificationRequests;
use App\Modules\Notifications\Models\NotificationRule;
use App\Modules\Notifications\Services\NotificationRuleService;
use App\Modules\Notifications\Services\NotificationsAccessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotificationRuleController extends Controller
{
    use PreparesNotificationRequests;

    public function index(Request $request): View
    {
        $context = NotificationsAccessContext::forUser($request->user());
        $rules = $context->applyScope(NotificationRule::query()->with(['farm']), 'notification_rules')->orderBy('source_module')->orderBy('name')->get();

        return view('notifications::rules.index', ['rules' => $rules]);
    }

    public function create(Request $request): View
    {
        $context = NotificationsAccessContext::forUser($request->user());

        return view('notifications::rules.create', ['rule' => new NotificationRule(), 'organizations' => $context->organizations(), 'farms' => $context->farms()]);
    }

    public function store(Request $request, NotificationRuleService $service): RedirectResponse
    {
        $context = NotificationsAccessContext::forUser($request->user());
        $data = $this->validated($request);
        $context->filters($data);
        $service->store($data, $request->user());

        return redirect()->route('notifications.rules.index')->with('status', 'Notification rule created.');
    }

    public function edit(Request $request, NotificationRule $rule): View
    {
        $context = $this->authorizeRule($request, $rule);

        return view('notifications::rules.edit', ['rule' => $rule, 'organizations' => $context->organizations(), 'farms' => $context->farms($rule->organization_id)]);
    }

    public function update(Request $request, NotificationRule $rule, NotificationRuleService $service): RedirectResponse
    {
        $context = $this->authorizeRule($request, $rule);
        $data = $this->validated($request);
        $context->filters($data);
        $service->update($rule, $data, $request->user());

        return redirect()->route('notifications.rules.index')->with('status', 'Notification rule updated.');
    }

    public function toggle(Request $request, NotificationRule $rule, NotificationRuleService $service): RedirectResponse
    {
        $this->authorizeRule($request, $rule);
        $service->toggle($rule, $request->user());

        return back()->with('status', 'Notification rule toggled.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer'],
            'farm_id' => ['nullable', 'integer'],
            'code' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source_module' => ['required', 'string', 'max:80'],
            'signal_type' => ['required', 'string', 'max:120'],
            'severity' => ['required', Rule::in(NotificationRuleService::SEVERITIES)],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'json'],
        ]) + ['is_active' => false];
    }
}
