<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->string('activity_type');
            $table->date('activity_date');
            $table->string('status')->default('recorded');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'farm_id', 'activity_type']);
            $table->index(['crop_cycle_id', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_activities');
    }
};
