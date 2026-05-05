<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_scouting_observations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->foreignId('crop_activity_id')->nullable()->constrained('crop_activities')->nullOnDelete();
            $table->date('observation_date');
            $table->string('observation_type');
            $table->string('severity')->nullable();
            $table->decimal('affected_area', 12, 2)->nullable();
            $table->string('affected_area_unit')->nullable();
            $table->string('pest_or_disease')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('recommendation')->nullable();
            $table->foreignId('observed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_scouting_observations');
    }
};
