<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Audit\Models\AuditActivityLog;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Seeder;

class AuditActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $admin = User::query()->where('email', 'admin@smartshamba.test')->first();

        if (! $organization || ! $admin) {
            return;
        }

        $samples = [
            ['reports', 'viewed', 'Viewed reports dashboard', 'Demo admin viewed the reports dashboard.', null],
            ['finance', 'created', 'Created cost entry', 'Demo admin created a farm cost entry.', 'Cost entry #1'],
            ['livestock', 'updated', 'Updated livestock treatment', 'Demo manager updated a livestock treatment record.', 'Treatment #1'],
            ['tasks', 'status_changed', 'Completed task', 'Demo worker completed a farm task.', 'Task #1'],
            ['documents', 'created', 'Uploaded document', 'Demo admin uploaded a document attachment.', 'Demo document'],
        ];

        foreach ($samples as $index => [$module, $event, $label, $description, $subject]) {
            AuditActivityLog::query()->updateOrCreate(
                [
                    'module' => $module,
                    'event' => $event,
                    'subject_label' => $subject,
                ],
                [
                    'organization_id' => $organization->id,
                    'farm_id' => $farm?->id,
                    'actor_user_id' => $admin->id,
                    'actor_name' => $admin->name,
                    'actor_email' => $admin->email,
                    'action_label' => $label,
                    'subject_type' => $subject ? 'demo' : null,
                    'subject_id' => $subject ? $index + 1 : null,
                    'description' => $description,
                    'metadata' => ['seeded' => true],
                    'request_method' => 'GET',
                    'request_url' => 'local-seed',
                    'occurred_at' => now()->subHours($index + 1),
                ],
            );
        }
    }
}
