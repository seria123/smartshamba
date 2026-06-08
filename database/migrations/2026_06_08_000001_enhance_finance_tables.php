<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenues', function (Blueprint $table) {
            if (! Schema::hasColumn('revenues', 'income_type')) {
                $table->string('income_type')->default('crop_sale')->after('buyer_id');
            }

            if (! Schema::hasColumn('revenues', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('invoice_number');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'livestock_id')) {
                $table->foreignId('livestock_id')->nullable()->after('crop_id')->constrained('livestock')->nullOnDelete();
            }

            if (! Schema::hasColumn('expenses', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('receipt_number');
            }

            if (! Schema::hasColumn('expenses', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('receipt_path');
            }

            if (! Schema::hasColumn('expenses', 'recurrence_interval')) {
                $table->string('recurrence_interval')->nullable()->after('is_recurring');
            }

            if (! Schema::hasColumn('expenses', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->after('recurrence_interval');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'livestock_id')) {
                $table->dropConstrainedForeignId('livestock_id');
            }

            foreach (['receipt_path', 'is_recurring', 'recurrence_interval', 'next_due_date'] as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('revenues', function (Blueprint $table) {
            foreach (['income_type', 'receipt_path'] as $column) {
                if (Schema::hasColumn('revenues', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
