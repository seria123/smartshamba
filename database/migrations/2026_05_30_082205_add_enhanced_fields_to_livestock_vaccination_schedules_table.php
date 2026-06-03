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
        Schema::table('livestock_vaccination_schedules', function (Blueprint $table) {
            // Dynamic form enhancements
            $table->decimal('animal_weight', 8, 2)->nullable()->after('livestock_type_id');
            $table->string('route', 20)->nullable()->after('animal_weight'); // IM, SC, oral, etc.
            $table->decimal('dose_amount', 8, 2)->nullable()->after('route');
            $table->string('dose_unit', 20)->nullable()->after('dose_amount'); // ml, mg, etc.
            
            // Evidence capture
            $table->string('evidence_photo_path', 255)->nullable()->after('notes');
            $table->string('qr_code_data', 255)->nullable()->after('evidence_photo_path');
            $table->text('vet_notes')->nullable()->after('qr_code_data');
            
            // Smart scheduling & AI recommendations
            $table->foreignId('parent_schedule_id')->nullable()->constrained('livestock_vaccination_schedules')->nullOnDelete()->after('vet_notes');
            $table->string('location', 100)->nullable()->after('parent_schedule_id'); // For disease trend analysis
            $table->string('season', 50)->nullable()->after('location'); // For seasonal recommendations
            $table->boolean('is_bulk_entry')->default(false)->after('season');
            
            // Alerts & risk flags
            $table->enum('alert_status', ['none', 'upcoming', 'due_soon', 'overdue', 'contraindication', 'expired_vaccine'])->default('none')->after('is_bulk_entry');
            $table->dateTime('last_synced_at')->nullable()->after('alert_status'); // For offline mode
            
            // Multi-user support
            $table->enum('user_role', ['admin', 'vet', 'worker'])->default('worker')->after('last_synced_at');
            $table->foreignId('vet_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('user_role');
            $table->dateTime('vet_approved_at')->nullable()->after('vet_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock_vaccination_schedules', function (Blueprint $table) {
            $table->dropColumn(['animal_weight', 'route', 'dose_amount', 'dose_unit']);
            $table->dropColumn(['evidence_photo_path', 'qr_code_data', 'vet_notes']);
            $table->dropColumn(['parent_schedule_id', 'location', 'season', 'is_bulk_entry']);
            $table->dropColumn(['alert_status', 'last_synced_at']);
            $table->dropColumn(['user_role', 'vet_approved_by', 'vet_approved_at']);
            $table->dropForeign(['livestock_vaccination_schedules_parent_schedule_id_foreign']);
            $table->dropForeign(['livestock_vaccination_schedules_vet_approved_by_foreign']);
        });
    }
};
