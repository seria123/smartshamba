<?php

namespace App\Modules\Audit\Services;

use App\Modules\Audit\Models\AuditActivityLog;
use Illuminate\Support\Str;

class AuditSubjectResolver
{
    public function label(AuditActivityLog $log): string
    {
        if ($log->subject_label) {
            return $log->subject_label;
        }

        if (! $log->subject_type || ! $log->subject_id) {
            return 'No subject';
        }

        $safeTypes = [
            'App\\Models\\User',
            'App\\Modules\\Core\\Models\\Organization',
            'App\\Modules\\Core\\Models\\Farm',
            'App\\Modules\\Documents\\Models\\FarmAttachment',
        ];

        if (! in_array($log->subject_type, $safeTypes, true) || ! class_exists($log->subject_type)) {
            return Str::headline(class_basename($log->subject_type)).' #'.$log->subject_id;
        }

        $record = $log->subject_type::query()->find($log->subject_id);

        return $record?->name
            ?? $record?->title
            ?? $record?->email
            ?? Str::headline(class_basename($log->subject_type)).' #'.$log->subject_id;
    }
}
