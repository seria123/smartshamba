<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_fumigation_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fumigant_name');
            $table->string('fumigation_type')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('liters');
            $table->string('area_covered')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('performed_date')->nullable();
            $table->string('performed_by')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'missed', 'cancelled'])->default('scheduled');
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('next_due_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_fumigation_schedules');
    }
};