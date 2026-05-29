<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pest_control_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_cycle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pest_name');
            $table->string('schedule_type')->default('monitoring');
            $table->date('scheduled_date');
            $table->date('next_schedule_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->text('treatment_notes')->nullable();
            $table->string('severity_level')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pest_control_schedules');
    }
};