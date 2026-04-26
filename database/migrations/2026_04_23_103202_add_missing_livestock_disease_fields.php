<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('livestock_diseases', function (Blueprint $table) {
            // Add missing foreign keys
            if (! Schema::hasColumn('livestock_diseases', 'livestock_id')) {
                $table->foreignId('livestock_id')->constrained('livestock')->onDelete('cascade')->after('id');
            }

            if (! Schema::hasColumn('livestock_diseases', 'disease_id')) {
                $table->foreignId('disease_id')->nullable()->constrained('diseases')->nullOnDelete()->after('livestock_id');
            }

            // Add tracking fields
            if (! Schema::hasColumn('livestock_diseases', 'status')) {
                $table->string('status')->default('active')->after('treatment');
            }

            if (! Schema::hasColumn('livestock_diseases', 'diagnosed_date')) {
                $table->date('diagnosed_date')->nullable()->after('status');
            }

            if (! Schema::hasColumn('livestock_diseases', 'treated_date')) {
                $table->date('treated_date')->nullable()->after('diagnosed_date');
            }

            if (! Schema::hasColumn('livestock_diseases', 'treated_by')) {
                $table->foreignId('treated_by')->nullable()->constrained('users')->nullOnDelete()->after('treated_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock_diseases', function (Blueprint $table) {
            $table->dropForeign(['livestock_id']);
            $table->dropForeign(['disease_id']);
            $table->dropForeign(['treated_by']);

            $table->dropColumn([
                'livestock_id',
                'disease_id',
                'status',
                'diagnosed_date',
                'treated_date',
                'treated_by',
            ]);
        });
    }
};
