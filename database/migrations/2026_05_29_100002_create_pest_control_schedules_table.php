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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pest_name');
            $table->text('description')->nullable();
            $table->string('threat_level')->default('medium');
            $table->date('scheduled_date')->nullable();
            $table->date('inspected_date')->nullable();
            $table->date('treatment_date')->nullable();
            $table->string('treatment_method')->nullable();
            $table->text('treatment_notes')->nullable();
            $table->enum('status', ['scheduled', 'inspected', 'treated', 'cancelled'])->default('scheduled');
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pest_control_schedules');
    }
};