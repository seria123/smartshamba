<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_vaccination_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('livestock_id')->nullable()->constrained('livestock')->nullOnDelete();
            $table->foreignId('livestock_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vaccine_name');
            $table->string('vaccine_type')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('administered_date')->nullable();
            $table->string('administered_by')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'administered', 'missed', 'cancelled'])->default('scheduled');
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('next_due_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_vaccination_schedules');
    }
};