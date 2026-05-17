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
        Schema::table('activities', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('activities', 'quantity')) {
                $table->decimal('quantity', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('activities', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('activities', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            }
            if (!Schema::hasColumn('activities', 'supervisor_id')) {
                $table->foreignId('supervisor_id')->nullable()->constrained('staff')->onDelete('set null');
            }
            // Add index if not exists
            $table->index(['status', 'activity_date'], 'activities_status_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['quantity', 'notes', 'status', 'supervisor_id']);
            $table->dropIndex(['status', 'activity_date']);
        });
    }
};
