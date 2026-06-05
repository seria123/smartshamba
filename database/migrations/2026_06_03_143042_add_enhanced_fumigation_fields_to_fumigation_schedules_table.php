<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fumigation_schedules', function (Blueprint $table) {
            $table->string('enclosure_type')->nullable()->after('location');
            $table->string('sealing_status')->nullable()->after('enclosure_type');
            $table->string('ventilation_method')->nullable()->after('sealing_status');
            $table->integer('rei_hours')->nullable()->after('ventilation_method');
            $table->timestamp('safe_entry_at')->nullable()->after('rei_hours');
            $table->timestamp('actual_start_at')->nullable()->after('safe_entry_at');
            $table->timestamp('actual_end_at')->nullable()->after('actual_start_at');
            $table->string('pest_activity_before')->nullable()->after('actual_end_at');
            $table->string('pest_activity_after')->nullable()->after('pest_activity_before');
            $table->string('effectiveness_rating')->nullable()->after('pest_activity_after');
            $table->text('incident_report')->nullable()->after('effectiveness_rating');
            $table->text('operator_notes')->nullable()->after('incident_report');
            $table->string('compliance_status')->nullable()->after('operator_notes');
            $table->text('restricted_warning')->nullable()->after('compliance_status');
            $table->foreignId('staff_operator_id')->nullable()->after('restricted_warning')->constrained('staff')->nullOnDelete();
            $table->boolean('operator_certified')->nullable()->after('staff_operator_id');
            $table->timestamp('certification_expiry')->nullable()->after('operator_certified');
            $table->text('emergency_contacts')->nullable()->after('certification_expiry');
            $table->boolean('ppe_respirator')->default(false)->after('emergency_contacts');
            $table->boolean('ppe_gloves')->default(false)->after('ppe_respirator');
            $table->boolean('ppe_suit')->default(false)->after('ppe_gloves');
            $table->boolean('ppe_complete')->default(false)->after('ppe_suit');
            $table->boolean('ai_recommended')->nullable()->after('ppe_complete');
            $table->text('ai_suggestion')->nullable()->after('ai_recommended');
            $table->string('infestation_risk')->nullable()->after('ai_suggestion');
            $table->string('next_recommended_date')->nullable()->after('infestation_risk');
        });
    }

    public function down(): void
    {
        Schema::table('fumigation_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'enclosure_type','sealing_status','ventilation_method','rei_hours','safe_entry_at',
                'actual_start_at','actual_end_at','pest_activity_before','pest_activity_after',
                'effectiveness_rating','incident_report','operator_notes','compliance_status',
                'restricted_warning','staff_operator_id','operator_certified','certification_expiry',
                'emergency_contacts','ppe_respirator','ppe_gloves','ppe_suit','ppe_complete',
                'ai_recommended','ai_suggestion','infestation_risk','next_recommended_date',
            ]);
        });
    }
};
