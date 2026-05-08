<?php

namespace App\Modules\Reports\Services;

use App\Modules\Crops\Models\CropActivity;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Crops\Models\CropLossRecord;
use App\Modules\Crops\Models\CropTreatmentApplication;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Livestock\Models\LivestockEvent;
use App\Modules\Livestock\Models\LivestockFeedRecord;
use App\Modules\Livestock\Models\LivestockMortalityRecord;
use App\Modules\Livestock\Models\LivestockTreatmentRecord;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsWorkOrder;
use App\Modules\Workers\Models\LabourAttendanceRecord;

class OperationalActivityReportService
{
    use BuildsReportQueries;

    public function summary(array $filters): array
    {
        return [
            'sections' => [
                'Open work orders' => $this->applyScope(OpsWorkOrder::query(), $filters, 'ops_work_orders')->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'Open tasks' => $this->applyScope(OpsTask::query(), $filters, 'ops_tasks')->whereNotIn('status', ['completed', 'cancelled', 'approved'])->count(),
                'Completed tasks in period' => $this->applyDateRange($this->applyScope(OpsTask::query(), $filters, 'ops_tasks'), $filters, 'ops_tasks', 'completed_at')->whereNotNull('completed_at')->count(),
                'Crop activities in period' => $this->countByDate(CropActivity::query(), $filters, 'crop_activities', 'activity_date'),
                'Crop treatments in period' => $this->countByDate(CropTreatmentApplication::query(), $filters, 'crop_treatment_applications', 'application_date'),
                'Crop harvests in period' => $this->countByDate(CropHarvestRecord::query(), $filters, 'crop_harvest_records', 'harvest_date'),
                'Crop losses in period' => $this->countByDate(CropLossRecord::query(), $filters, 'crop_loss_records', 'loss_date'),
                'Livestock events in period' => $this->countByDate(LivestockEvent::query(), $filters, 'livestock_events', 'event_date'),
                'Livestock treatments in period' => $this->countByDate(LivestockTreatmentRecord::query(), $filters, 'livestock_treatment_records', 'treatment_date'),
                'Livestock feed records in period' => $this->countByDate(LivestockFeedRecord::query(), $filters, 'livestock_feed_records', 'feed_date'),
                'Livestock yields in period' => $this->countByDate(LivestockYieldRecord::query(), $filters, 'livestock_yield_records', 'yield_date'),
                'Livestock mortality records in period' => $this->countByDate(LivestockMortalityRecord::query(), $filters, 'livestock_mortality_records', 'mortality_date'),
                'Irrigation events in period' => $this->countByDate(IrrigationEvent::query(), $filters, 'irrigation_events', 'irrigation_date'),
                'Open irrigation issues' => $this->applyScope(IrrigationIssue::query(), $filters, 'irrigation_issues')->whereNotIn('status', ['resolved', 'cancelled'])->count(),
                'Attendance records in period' => $this->countByDate(LabourAttendanceRecord::query(), $filters, 'labour_attendance_records', 'date'),
            ],
            'openTasks' => $this->applyScope(OpsTask::query()->with('farm'), $filters, 'ops_tasks')->whereNotIn('status', ['completed', 'cancelled', 'approved'])->latest('due_date')->limit(10)->get(),
        ];
    }

    private function countByDate($query, array $filters, string $table, string $column): int
    {
        return $this->applyDateRange($this->applyScope($query, $filters, $table), $filters, $table, $column)->count();
    }
}
