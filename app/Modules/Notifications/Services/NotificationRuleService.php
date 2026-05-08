<?php

namespace App\Modules\Notifications\Services;

use App\Models\User;
use App\Modules\Notifications\Models\NotificationRule;

class NotificationRuleService
{
    public const SEVERITIES = ['low', 'medium', 'high', 'critical'];

    public function store(array $data, User $user): NotificationRule
    {
        $data['created_by_user_id'] = $user->id;
        $data['updated_by_user_id'] = $user->id;
        $data['settings'] = $this->decodeSettings($data['settings'] ?? null);

        return NotificationRule::query()->create($data);
    }

    public function update(NotificationRule $rule, array $data, User $user): NotificationRule
    {
        $data['updated_by_user_id'] = $user->id;
        $data['settings'] = $this->decodeSettings($data['settings'] ?? null);
        $rule->update($data);

        return $rule;
    }

    public function toggle(NotificationRule $rule, User $user): NotificationRule
    {
        $rule->update(['is_active' => ! $rule->is_active, 'updated_by_user_id' => $user->id]);

        return $rule;
    }

    private function decodeSettings(mixed $settings): ?array
    {
        if ($settings === null || $settings === '') {
            return null;
        }

        return is_array($settings) ? $settings : json_decode($settings, true, flags: JSON_THROW_ON_ERROR);
    }
}
