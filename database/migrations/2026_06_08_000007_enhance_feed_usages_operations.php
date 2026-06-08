<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE feed_usages MODIFY livestock_id BIGINT UNSIGNED NULL');

        Schema::table('feed_usages', function (Blueprint $table) {
            if (! Schema::hasColumn('feed_usages', 'group_name')) {
                $table->string('group_name')->nullable()->after('livestock_id');
            }

            if (! Schema::hasColumn('feed_usages', 'feeding_frequency')) {
                $table->string('feeding_frequency')->default('daily')->after('unit');
            }

            if (! Schema::hasColumn('feed_usages', 'feeding_time')) {
                $table->string('feeding_time')->nullable()->after('feeding_frequency');
            }

            if (! Schema::hasColumn('feed_usages', 'reminder_at')) {
                $table->dateTime('reminder_at')->nullable()->after('feeding_time');
            }

            if (! Schema::hasColumn('feed_usages', 'unit_conversion_factor')) {
                $table->decimal('unit_conversion_factor', 12, 4)->nullable()->after('reminder_at');
            }

            if (! Schema::hasColumn('feed_usages', 'unit_cost')) {
                $table->decimal('unit_cost', 12, 2)->nullable()->after('unit_conversion_factor');
            }

            if (! Schema::hasColumn('feed_usages', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('unit_cost');
            }

            if (! Schema::hasColumn('feed_usages', 'output_type')) {
                $table->string('output_type')->nullable()->after('total_cost');
            }

            if (! Schema::hasColumn('feed_usages', 'output_quantity')) {
                $table->decimal('output_quantity', 12, 2)->nullable()->after('output_type');
            }

            if (! Schema::hasColumn('feed_usages', 'output_unit')) {
                $table->string('output_unit')->nullable()->after('output_quantity');
            }

            if (! Schema::hasColumn('feed_usages', 'weight_gain')) {
                $table->decimal('weight_gain', 12, 2)->nullable()->after('output_unit');
            }

            if (! Schema::hasColumn('feed_usages', 'feed_conversion_ratio')) {
                $table->decimal('feed_conversion_ratio', 12, 4)->nullable()->after('weight_gain');
            }

            if (! Schema::hasColumn('feed_usages', 'deduct_inventory')) {
                $table->boolean('deduct_inventory')->default(true)->after('feed_conversion_ratio');
            }

            if (! Schema::hasColumn('feed_usages', 'inventory_deducted')) {
                $table->boolean('inventory_deducted')->default(false)->after('deduct_inventory');
            }

            if (! Schema::hasColumn('feed_usages', 'stock_before')) {
                $table->decimal('stock_before', 12, 2)->nullable()->after('inventory_deducted');
            }

            if (! Schema::hasColumn('feed_usages', 'stock_after')) {
                $table->decimal('stock_after', 12, 2)->nullable()->after('stock_before');
            }

            if (! Schema::hasColumn('feed_usages', 'trend_change_percent')) {
                $table->decimal('trend_change_percent', 8, 2)->nullable()->after('stock_after');
            }

            if (! Schema::hasColumn('feed_usages', 'anomaly_status')) {
                $table->string('anomaly_status')->nullable()->after('trend_change_percent');
            }

            if (! Schema::hasColumn('feed_usages', 'ai_recommendation')) {
                $table->text('ai_recommendation')->nullable()->after('anomaly_status');
            }

            if (! Schema::hasColumn('feed_usages', 'season')) {
                $table->string('season')->nullable()->after('ai_recommendation');
            }

            if (! Schema::hasColumn('feed_usages', 'production_stage')) {
                $table->string('production_stage')->nullable()->after('season');
            }

            if (! Schema::hasColumn('feed_usages', 'target_protein')) {
                $table->decimal('target_protein', 8, 2)->nullable()->after('production_stage');
            }

            if (! Schema::hasColumn('feed_usages', 'target_energy')) {
                $table->decimal('target_energy', 8, 2)->nullable()->after('target_protein');
            }

            if (! Schema::hasColumn('feed_usages', 'minerals')) {
                $table->json('minerals')->nullable()->after('target_energy');
            }

            if (! Schema::hasColumn('feed_usages', 'staff_id')) {
                $table->foreignId('staff_id')->nullable()->after('minerals')->constrained('staff')->nullOnDelete();
            }

            if (! Schema::hasColumn('feed_usages', 'quality_image_path')) {
                $table->string('quality_image_path')->nullable()->after('staff_id');
            }

            if (! Schema::hasColumn('feed_usages', 'quality_notes')) {
                $table->text('quality_notes')->nullable()->after('quality_image_path');
            }

            if (! Schema::hasColumn('feed_usages', 'spoilage_status')) {
                $table->string('spoilage_status')->nullable()->after('quality_notes');
            }

            if (! Schema::hasColumn('feed_usages', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('spoilage_status');
            }

            if (! Schema::hasColumn('feed_usages', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('batch_number')->constrained('suppliers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('feed_usages', function (Blueprint $table) {
            foreach (['staff_id', 'supplier_id'] as $column) {
                if (Schema::hasColumn('feed_usages', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'group_name', 'feeding_frequency', 'feeding_time', 'reminder_at',
                'unit_conversion_factor', 'unit_cost', 'total_cost',
                'output_type', 'output_quantity', 'output_unit', 'weight_gain', 'feed_conversion_ratio',
                'deduct_inventory', 'inventory_deducted', 'stock_before', 'stock_after',
                'trend_change_percent', 'anomaly_status', 'ai_recommendation',
                'season', 'production_stage', 'target_protein', 'target_energy', 'minerals',
                'quality_image_path', 'quality_notes', 'spoilage_status', 'batch_number',
            ] as $column) {
                if (Schema::hasColumn('feed_usages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
