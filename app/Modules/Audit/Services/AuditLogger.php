<?php

namespace App\Modules\Audit\Services;

use App\Modules\Audit\Models\AuditActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public function __construct(private readonly ?Request $request = null) {}

    public function record(array $data): AuditActivityLog
    {
        $user = Auth::user();
        $request = $this->request;

        return AuditActivityLog::create([
            'organization_id' => $data['organization_id'] ?? $data['organisation_id'] ?? null,
            'farm_id' => $data['farm_id'] ?? null,
            'actor_user_id' => $data['actor_user_id'] ?? $user?->id,
            'actor_name' => $data['actor_name'] ?? $user?->name,
            'actor_email' => $data['actor_email'] ?? $user?->email,
            'module' => $data['module'] ?? 'system',
            'event' => $data['event'] ?? 'activity',
            'action_label' => $data['action_label'] ?? null,
            'subject_type' => $data['subject_type'] ?? null,
            'subject_id' => $data['subject_id'] ?? null,
            'subject_label' => $data['subject_label'] ?? null,
            'description' => $data['description'] ?? null,
            'before_values' => $data['before_values'] ?? null,
            'after_values' => $data['after_values'] ?? null,
            'changed_values' => $data['changed_values'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'request_method' => $data['request_method'] ?? $request?->method(),
            'request_url' => $data['request_url'] ?? $request?->fullUrl(),
            'ip_address' => $data['ip_address'] ?? $request?->ip(),
            'user_agent' => $data['user_agent'] ?? $request?->userAgent(),
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);
    }

    public function created(string $module, ?Model $subject = null, array $data = []): AuditActivityLog
    {
        return $this->record($this->subjectPayload($module, 'created', $subject, $data));
    }

    public function updated(string $module, ?Model $subject = null, array $data = []): AuditActivityLog
    {
        return $this->record($this->subjectPayload($module, 'updated', $subject, $data));
    }

    public function deleted(string $module, ?Model $subject = null, array $data = []): AuditActivityLog
    {
        return $this->record($this->subjectPayload($module, 'deleted', $subject, $data));
    }

    public function viewed(string $module, ?Model $subject = null, array $data = []): AuditActivityLog
    {
        return $this->record($this->subjectPayload($module, 'viewed', $subject, $data));
    }

    public function statusChanged(string $module, ?Model $subject = null, array $data = []): AuditActivityLog
    {
        return $this->record($this->subjectPayload($module, 'status_changed', $subject, $data));
    }

    public function login(array $data = []): AuditActivityLog
    {
        return $this->record($data + ['module' => 'access', 'event' => 'login', 'action_label' => 'Login']);
    }

    public function logout(array $data = []): AuditActivityLog
    {
        return $this->record($data + ['module' => 'access', 'event' => 'logout', 'action_label' => 'Logout']);
    }

    private function subjectPayload(string $module, string $event, ?Model $subject, array $data): array
    {
        return $data + [
            'module' => $module,
            'event' => $event,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
        ];
    }
}
