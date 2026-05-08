<?php

namespace App\Modules\Notifications\Services;

use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Irrigation\Models\IrrigationSchedule;
use App\Modules\Livestock\Models\LivestockWithdrawalPeriod;
use App\Modules\Notifications\Models\FarmNotification;
use App\Modules\Notifications\Models\NotificationRule;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsWorkOrder;
use Illuminate\Database\Eloquent\Builder;

class NotificationSignalDetector
{
    public function __construct(private readonly NotificationSourceResolver $resolver) {}

    public function refresh(NotificationsAccessContext $context): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0];
        $rules = $context->applyScope(NotificationRule::query(), 'notification_rules')->where('is_active', true)->get();

        foreach ($rules as $rule) {
            $candidates = $this->candidates($rule, $context);
            if ($candidates === null) {
                $summary['skipped']++;
                continue;
            }

            foreach ($candidates as $candidate) {
                $result = $this->upsert($rule, $candidate);
                $summary[$result]++;
            }
        }

        return $summary;
    }

    private function candidates(NotificationRule $rule, NotificationsAccessContext $context): ?array
    {
        return match ($rule->signal_type) {
            'tasks.overdue' => $this->overdueTasks($rule, $context),
            'inventory.low_stock' => $this->lowStock($rule, $context),
            'inventory.expiry_soon' => $this->expiringLots($rule, $context),
            'livestock.withdrawal_active' => $this->activeWithdrawals($rule, $context),
            'assets.maintenance_due' => $this->dueAssetMaintenance($rule, $context),
            'assets.breakdown_open' => $this->openBreakdowns($rule, $context),
            'irrigation.schedule_due' => $this->dueIrrigation($rule, $context),
            'irrigation.issue_open' => $this->openIrrigationIssues($rule, $context),
            'finance.unconfirmed_cost' => $this->unconfirmedCosts($rule, $context),
            default => null,
        };
    }

    private function overdueTasks(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        $tasks = $this->scoped($context, OpsTask::query()->with('farm'), 'ops_tasks')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['completed', 'cancelled', 'approved'])
            ->get()
            ->map(fn (OpsTask $task) => $this->candidate($rule, 'ops_task', $task->id, $task->organization_id, $task->farm_id, 'Overdue task: '.$task->title, 'Task '.$task->task_number.' was due on '.$task->due_date?->toDateString().'.', $task->title, $task->due_date));

        $orders = $this->scoped($context, OpsWorkOrder::query()->with('farm'), 'ops_work_orders')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get()
            ->map(fn (OpsWorkOrder $order) => $this->candidate($rule, 'ops_work_order', $order->id, $order->organization_id, $order->farm_id, 'Overdue work order: '.$order->title, 'Work order '.$order->work_order_number.' was due on '.$order->due_date?->toDateString().'.', $order->title, $order->due_date));

        return $tasks->merge($orders)->all();
    }

    private function lowStock(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        return $this->scoped($context, InventoryStockLot::query()->with(['product', 'warehouse']), 'inventory_stock_lots')
            ->join('inventory_products', 'inventory_products.id', '=', 'inventory_stock_lots.product_id')
            ->whereColumn('inventory_stock_lots.quantity_on_hand', '<=', 'inventory_products.reorder_level')
            ->select('inventory_stock_lots.*')
            ->get()
            ->map(fn (InventoryStockLot $lot) => $this->candidate($rule, 'inventory_stock_lot', $lot->id, $lot->organization_id, $lot->farm_id, 'Low stock: '.($lot->product?->name ?? 'Stock lot #'.$lot->id), 'Quantity on hand is '.$lot->quantity_on_hand.' '.$lot->unit_of_measure.'.', $lot->product?->name ?? 'Stock lot #'.$lot->id))
            ->all();
    }

    private function expiringLots(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        $days = (int) ($rule->settings['days'] ?? 30);

        return $this->scoped($context, InventoryStockLot::query()->with('product'), 'inventory_stock_lots')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', today()->addDays($days))
            ->whereDate('expiry_date', '>=', today())
            ->get()
            ->map(fn (InventoryStockLot $lot) => $this->candidate($rule, 'inventory_stock_lot', $lot->id, $lot->organization_id, $lot->farm_id, 'Inventory lot expires soon: '.($lot->product?->name ?? 'Stock lot #'.$lot->id), 'Lot '.$lot->lot_number.' expires on '.$lot->expiry_date?->toDateString().'.', $lot->product?->name ?? 'Stock lot #'.$lot->id, $lot->expiry_date))
            ->all();
    }

    private function activeWithdrawals(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        return $this->scoped($context, LivestockWithdrawalPeriod::query()->with(['animal', 'animalGroup']), 'livestock_withdrawal_periods')
            ->whereDate('starts_on', '<=', today())
            ->whereDate('ends_on', '>=', today())
            ->whereNotIn('status', ['completed', 'cancelled', 'expired'])
            ->get()
            ->map(function (LivestockWithdrawalPeriod $period) use ($rule): array {
                $label = $period->animal?->name ?: $period->animal?->tag_number ?: $period->animalGroup?->name ?: 'Withdrawal #'.$period->id;
                return $this->candidate($rule, 'livestock_withdrawal_period', $period->id, $period->organization_id, $period->farm_id, 'Withdrawal active: '.$label, 'Withdrawal period is active until '.$period->ends_on?->toDateString().'.', $label, $period->ends_on);
            })->all();
    }

    private function dueAssetMaintenance(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        $days = (int) ($rule->settings['days'] ?? 7);

        return $this->scoped($context, AssetMaintenanceSchedule::query()->with('asset'), 'asset_maintenance_schedules')
            ->whereDate('scheduled_date', '<=', today()->addDays($days))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get()
            ->map(fn (AssetMaintenanceSchedule $schedule) => $this->candidate($rule, 'asset_maintenance_schedule', $schedule->id, $schedule->organization_id, $schedule->farm_id, 'Asset maintenance due: '.($schedule->asset?->name ?? $schedule->schedule_number), 'Maintenance is scheduled for '.$schedule->scheduled_date?->toDateString().'.', $schedule->asset?->name ?? $schedule->schedule_number, $schedule->scheduled_date))
            ->all();
    }

    private function openBreakdowns(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        return $this->scoped($context, AssetBreakdownRecord::query()->with('asset'), 'asset_breakdown_records')
            ->whereNotIn('status', ['resolved', 'cancelled'])
            ->get()
            ->map(fn (AssetBreakdownRecord $breakdown) => $this->candidate($rule, 'asset_breakdown_record', $breakdown->id, $breakdown->organization_id, $breakdown->farm_id, 'Open asset breakdown: '.($breakdown->asset?->name ?? $breakdown->breakdown_number), $breakdown->description ?: 'Asset breakdown remains open.', $breakdown->asset?->name ?? $breakdown->breakdown_number, $breakdown->breakdown_date))
            ->all();
    }

    private function dueIrrigation(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        $days = (int) ($rule->settings['days'] ?? 3);

        return $this->scoped($context, IrrigationSchedule::query()->with('zone'), 'irrigation_schedules')
            ->whereDate('scheduled_date', '<=', today()->addDays($days))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get()
            ->map(fn (IrrigationSchedule $schedule) => $this->candidate($rule, 'irrigation_schedule', $schedule->id, $schedule->organization_id, $schedule->farm_id, 'Irrigation due: '.($schedule->zone?->name ?? $schedule->schedule_number), 'Irrigation is scheduled for '.$schedule->scheduled_date?->toDateString().'.', $schedule->zone?->name ?? $schedule->schedule_number, $schedule->scheduled_date))
            ->all();
    }

    private function openIrrigationIssues(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        return $this->scoped($context, IrrigationIssue::query()->with('zone'), 'irrigation_issues')
            ->whereNotIn('status', ['resolved', 'cancelled'])
            ->get()
            ->map(fn (IrrigationIssue $issue) => $this->candidate($rule, 'irrigation_issue', $issue->id, $issue->organization_id, $issue->farm_id, 'Open irrigation issue: '.($issue->zone?->name ?? $issue->issue_number), $issue->description ?: 'Irrigation issue remains open.', $issue->zone?->name ?? $issue->issue_number, $issue->issue_date))
            ->all();
    }

    private function unconfirmedCosts(NotificationRule $rule, NotificationsAccessContext $context): array
    {
        return $this->scoped($context, FinanceCostEntry::query(), 'finance_cost_entries')
            ->whereIn('status', ['draft', 'pending'])
            ->get()
            ->map(fn (FinanceCostEntry $entry) => $this->candidate($rule, 'finance_cost_entry', $entry->id, $entry->organization_id, $entry->farm_id, 'Cost awaiting confirmation: '.$entry->title, 'Cost entry '.$entry->entry_no.' is '.$entry->status.'.', $entry->title, $entry->entry_date))
            ->all();
    }

    private function scoped(NotificationsAccessContext $context, Builder $query, string $table): Builder
    {
        return $context->applyScope($query, $table);
    }

    private function candidate(NotificationRule $rule, string $sourceType, int $sourceId, int $organizationId, ?int $farmId, string $title, string $message, string $label, mixed $dueAt = null): array
    {
        return [
            'organization_id' => $organizationId,
            'farm_id' => $farmId,
            'source_module' => $rule->source_module,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'signal_type' => $rule->signal_type,
            'title' => $title,
            'message' => $message,
            'severity' => $rule->severity,
            'source_label' => $label,
            'action_url' => $this->resolver->url($sourceType, $sourceId),
            'due_at' => $dueAt,
        ];
    }

    private function upsert(NotificationRule $rule, array $candidate): string
    {
        $existing = FarmNotification::query()
            ->where('organization_id', $candidate['organization_id'])
            ->where('farm_id', $candidate['farm_id'])
            ->where('source_module', $candidate['source_module'])
            ->where('source_type', $candidate['source_type'])
            ->where('source_id', $candidate['source_id'])
            ->where('signal_type', $candidate['signal_type'])
            ->whereNotIn('status', ['dismissed', 'resolved'])
            ->first();

        $payload = $candidate + [
            'notification_rule_id' => $rule->id,
            'status' => 'unread',
            'generated_at' => now(),
        ];

        if ($existing) {
            $existing->update(collect($payload)->except('status')->all());
            return 'updated';
        }

        FarmNotification::query()->create($payload);
        return 'created';
    }
}
