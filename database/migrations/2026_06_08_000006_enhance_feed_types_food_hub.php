<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_types', function (Blueprint $table) {
            if (! Schema::hasColumn('feed_types', 'category')) {
                $table->string('category')->default('crop_based')->after('name');
            }

            if (! Schema::hasColumn('feed_types', 'sub_category')) {
                $table->string('sub_category')->nullable()->after('category');
            }

            if (! Schema::hasColumn('feed_types', 'unit_conversion_label')) {
                $table->string('unit_conversion_label')->nullable()->after('default_unit');
            }

            if (! Schema::hasColumn('feed_types', 'unit_conversion_factor')) {
                $table->decimal('unit_conversion_factor', 12, 4)->nullable()->after('unit_conversion_label');
            }

            if (! Schema::hasColumn('feed_types', 'selling_price')) {
                $table->decimal('selling_price', 12, 2)->nullable()->after('min_threshold');
            }

            if (! Schema::hasColumn('feed_types', 'minimum_price')) {
                $table->decimal('minimum_price', 12, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('feed_types', 'market_price')) {
                $table->decimal('market_price', 12, 2)->nullable()->after('minimum_price');
            }

            if (! Schema::hasColumn('feed_types', 'price_history')) {
                $table->json('price_history')->nullable()->after('market_price');
            }

            if (! Schema::hasColumn('feed_types', 'linked_crop_id')) {
                $table->foreignId('linked_crop_id')->nullable()->after('price_history')->constrained('crops')->nullOnDelete();
            }

            if (! Schema::hasColumn('feed_types', 'linked_livestock_id')) {
                $table->foreignId('linked_livestock_id')->nullable()->after('linked_crop_id')->constrained('livestock')->nullOnDelete();
            }

            if (! Schema::hasColumn('feed_types', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('linked_livestock_id')->constrained('suppliers')->nullOnDelete();
            }

            foreach (['protein', 'carbohydrates', 'fats', 'energy_calories'] as $column) {
                if (! Schema::hasColumn('feed_types', $column)) {
                    $table->decimal($column, 10, 2)->nullable();
                }
            }

            if (! Schema::hasColumn('feed_types', 'vitamins')) {
                $table->json('vitamins')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'expiry_period_days')) {
                $table->unsignedInteger('expiry_period_days')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'storage_conditions')) {
                $table->json('storage_conditions')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'raw_input_name')) {
                $table->string('raw_input_name')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'processed_output_name')) {
                $table->string('processed_output_name')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'processing_cost')) {
                $table->decimal('processing_cost', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'yield_ratio')) {
                $table->decimal('yield_ratio', 8, 4)->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'input_cost')) {
                $table->decimal('input_cost', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'labor_cost')) {
                $table->decimal('labor_cost', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'batch_number')) {
                $table->string('batch_number')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'production_date')) {
                $table->date('production_date')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'source_batch')) {
                $table->string('source_batch')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'demand_level')) {
                $table->string('demand_level')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'best_selling_periods')) {
                $table->json('best_selling_periods')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'buyer_preferences')) {
                $table->text('buyer_preferences')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'market_regions')) {
                $table->json('market_regions')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'region_pricing')) {
                $table->json('region_pricing')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'image_path')) {
                $table->string('image_path')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'quality_grade')) {
                $table->string('quality_grade')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'certifications')) {
                $table->json('certifications')->nullable();
            }

            if (! Schema::hasColumn('feed_types', 'user_notes')) {
                $table->text('user_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('feed_types', function (Blueprint $table) {
            foreach (['linked_crop_id', 'linked_livestock_id', 'supplier_id'] as $column) {
                if (Schema::hasColumn('feed_types', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'category', 'sub_category', 'unit_conversion_label', 'unit_conversion_factor',
                'selling_price', 'minimum_price', 'market_price', 'price_history',
                'protein', 'carbohydrates', 'fats', 'energy_calories', 'vitamins',
                'expiry_period_days', 'storage_conditions',
                'raw_input_name', 'processed_output_name', 'processing_cost', 'yield_ratio',
                'input_cost', 'labor_cost',
                'batch_number', 'production_date', 'source_batch',
                'demand_level', 'best_selling_periods', 'buyer_preferences',
                'market_regions', 'region_pricing', 'image_path', 'quality_grade',
                'certifications', 'user_notes',
            ] as $column) {
                if (Schema::hasColumn('feed_types', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
