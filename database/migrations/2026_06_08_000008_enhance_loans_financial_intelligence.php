<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (! Schema::hasColumn('loans', 'loan_name')) {
                $table->string('loan_name')->nullable()->after('farm_id');
            }

            if (! Schema::hasColumn('loans', 'purpose_tag')) {
                $table->string('purpose_tag')->nullable()->after('loan_name');
            }

            if (! Schema::hasColumn('loans', 'lender_type')) {
                $table->string('lender_type')->nullable()->after('lender_name');
            }

            if (! Schema::hasColumn('loans', 'lender_contact')) {
                $table->string('lender_contact')->nullable()->after('lender_type');
            }

            if (! Schema::hasColumn('loans', 'duration_months')) {
                $table->unsignedInteger('duration_months')->nullable()->after('interest_rate');
            }

            if (! Schema::hasColumn('loans', 'interest_method')) {
                $table->string('interest_method')->default('flat')->after('duration_months');
            }

            if (! Schema::hasColumn('loans', 'repayment_schedule')) {
                $table->json('repayment_schedule')->nullable()->after('repayment_frequency');
            }

            if (! Schema::hasColumn('loans', 'payments')) {
                $table->json('payments')->nullable()->after('repayment_schedule');
            }

            if (! Schema::hasColumn('loans', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payments');
            }

            if (! Schema::hasColumn('loans', 'remaining_principal')) {
                $table->decimal('remaining_principal', 12, 2)->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('loans', 'total_interest_paid')) {
                $table->decimal('total_interest_paid', 12, 2)->default(0)->after('remaining_principal');
            }

            if (! Schema::hasColumn('loans', 'crop_cycle_id')) {
                $table->foreignId('crop_cycle_id')->nullable()->after('total_interest_paid')->constrained('crop_cycles')->nullOnDelete();
            }

            if (! Schema::hasColumn('loans', 'livestock_id')) {
                $table->foreignId('livestock_id')->nullable()->after('crop_cycle_id')->constrained('livestock')->nullOnDelete();
            }

            if (! Schema::hasColumn('loans', 'allocation_inputs')) {
                $table->decimal('allocation_inputs', 12, 2)->default(0)->after('livestock_id');
            }

            if (! Schema::hasColumn('loans', 'allocation_labor')) {
                $table->decimal('allocation_labor', 12, 2)->default(0)->after('allocation_inputs');
            }

            if (! Schema::hasColumn('loans', 'allocation_equipment')) {
                $table->decimal('allocation_equipment', 12, 2)->default(0)->after('allocation_labor');
            }

            if (! Schema::hasColumn('loans', 'expected_profit')) {
                $table->decimal('expected_profit', 12, 2)->nullable()->after('allocation_equipment');
            }

            if (! Schema::hasColumn('loans', 'generated_profit')) {
                $table->decimal('generated_profit', 12, 2)->nullable()->after('expected_profit');
            }

            if (! Schema::hasColumn('loans', 'farm_income_snapshot')) {
                $table->decimal('farm_income_snapshot', 12, 2)->nullable()->after('generated_profit');
            }

            if (! Schema::hasColumn('loans', 'risk_level')) {
                $table->string('risk_level')->nullable()->after('farm_income_snapshot');
            }

            if (! Schema::hasColumn('loans', 'risk_notes')) {
                $table->text('risk_notes')->nullable()->after('risk_level');
            }

            if (! Schema::hasColumn('loans', 'early_repayment_extra')) {
                $table->decimal('early_repayment_extra', 12, 2)->nullable()->after('risk_notes');
            }

            if (! Schema::hasColumn('loans', 'interest_saved_estimate')) {
                $table->decimal('interest_saved_estimate', 12, 2)->nullable()->after('early_repayment_extra');
            }

            if (! Schema::hasColumn('loans', 'comparison_notes')) {
                $table->text('comparison_notes')->nullable()->after('interest_saved_estimate');
            }

            if (! Schema::hasColumn('loans', 'document_paths')) {
                $table->json('document_paths')->nullable()->after('comparison_notes');
            }

            if (! Schema::hasColumn('loans', 'late_penalty_rate')) {
                $table->decimal('late_penalty_rate', 5, 2)->default(0)->after('document_paths');
            }

            if (! Schema::hasColumn('loans', 'extra_charges')) {
                $table->decimal('extra_charges', 12, 2)->default(0)->after('late_penalty_rate');
            }

            if (! Schema::hasColumn('loans', 'group_loan_members')) {
                $table->json('group_loan_members')->nullable()->after('extra_charges');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            foreach (['crop_cycle_id', 'livestock_id'] as $column) {
                if (Schema::hasColumn('loans', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'loan_name', 'purpose_tag', 'lender_type', 'lender_contact',
                'duration_months', 'interest_method', 'repayment_schedule', 'payments',
                'payment_method', 'remaining_principal', 'total_interest_paid',
                'allocation_inputs', 'allocation_labor', 'allocation_equipment',
                'expected_profit', 'generated_profit', 'farm_income_snapshot',
                'risk_level', 'risk_notes', 'early_repayment_extra', 'interest_saved_estimate',
                'comparison_notes', 'document_paths', 'late_penalty_rate', 'extra_charges',
                'group_loan_members',
            ] as $column) {
                if (Schema::hasColumn('loans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
