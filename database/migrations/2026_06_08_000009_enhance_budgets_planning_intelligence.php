<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            if (! Schema::hasColumn('budgets', 'season_name')) {
                $table->string('season_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('budgets', 'crop_cycle_id')) {
                $table->foreignId('crop_cycle_id')->nullable()->after('crop_id')->constrained('crop_cycles')->nullOnDelete();
            }

            if (! Schema::hasColumn('budgets', 'loan_id')) {
                $table->foreignId('loan_id')->nullable()->after('livestock_id')->constrained('loans')->nullOnDelete();
            }

            if (! Schema::hasColumn('budgets', 'owner_user_id')) {
                $table->foreignId('owner_user_id')->nullable()->after('loan_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('budgets', 'owner_group_name')) {
                $table->string('owner_group_name')->nullable()->after('owner_user_id');
            }

            if (! Schema::hasColumn('budgets', 'responsible_role')) {
                $table->string('responsible_role')->nullable()->after('owner_group_name');
            }

            if (! Schema::hasColumn('budgets', 'line_items')) {
                $table->json('line_items')->nullable()->after('category');
            }

            if (! Schema::hasColumn('budgets', 'actual_amount_override')) {
                $table->decimal('actual_amount_override', 12, 2)->nullable()->after('planned_amount');
            }

            if (! Schema::hasColumn('budgets', 'allow_overspend')) {
                $table->boolean('allow_overspend')->default(true)->after('alert_threshold_percent');
            }

            if (! Schema::hasColumn('budgets', 'alert_thresholds')) {
                $table->json('alert_thresholds')->nullable()->after('allow_overspend');
            }

            if (! Schema::hasColumn('budgets', 'forecast_amount')) {
                $table->decimal('forecast_amount', 12, 2)->nullable()->after('alert_thresholds');
            }

            if (! Schema::hasColumn('budgets', 'expected_income')) {
                $table->decimal('expected_income', 12, 2)->nullable()->after('forecast_amount');
            }

            if (! Schema::hasColumn('budgets', 'generated_profit')) {
                $table->decimal('generated_profit', 12, 2)->nullable()->after('expected_income');
            }

            if (! Schema::hasColumn('budgets', 'market_price_assumption')) {
                $table->decimal('market_price_assumption', 12, 2)->nullable()->after('generated_profit');
            }

            if (! Schema::hasColumn('budgets', 'what_if_scenarios')) {
                $table->json('what_if_scenarios')->nullable()->after('market_price_assumption');
            }

            if (! Schema::hasColumn('budgets', 'ai_suggestions')) {
                $table->text('ai_suggestions')->nullable()->after('what_if_scenarios');
            }

            if (! Schema::hasColumn('budgets', 'cost_efficiency_notes')) {
                $table->text('cost_efficiency_notes')->nullable()->after('ai_suggestions');
            }

            if (! Schema::hasColumn('budgets', 'approval_status')) {
                $table->string('approval_status')->default('draft')->after('cost_efficiency_notes');
            }

            if (! Schema::hasColumn('budgets', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('budgets', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('budgets', 'adjustment_log')) {
                $table->json('adjustment_log')->nullable()->after('approved_at');
            }

            if (! Schema::hasColumn('budgets', 'document_paths')) {
                $table->json('document_paths')->nullable()->after('adjustment_log');
            }

            if (! Schema::hasColumn('budgets', 'inventory_link_notes')) {
                $table->text('inventory_link_notes')->nullable()->after('document_paths');
            }
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            foreach (['crop_cycle_id', 'loan_id', 'owner_user_id', 'approved_by'] as $column) {
                if (Schema::hasColumn('budgets', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'season_name', 'owner_group_name', 'responsible_role', 'line_items',
                'actual_amount_override', 'allow_overspend', 'alert_thresholds',
                'forecast_amount', 'expected_income', 'generated_profit',
                'market_price_assumption', 'what_if_scenarios', 'ai_suggestions',
                'cost_efficiency_notes', 'approval_status', 'approved_at',
                'adjustment_log', 'document_paths', 'inventory_link_notes',
            ] as $column) {
                if (Schema::hasColumn('budgets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
