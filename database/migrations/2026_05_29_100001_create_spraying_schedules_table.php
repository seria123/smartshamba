<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spraying_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('spray_type');
            $table->string('chemical_name');
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('liters');
            $table->date('scheduled_date')->nullable();
            $table->date('applied_date')->nullable();
            $table->string('application_method')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spraying_schedules');
    }
};