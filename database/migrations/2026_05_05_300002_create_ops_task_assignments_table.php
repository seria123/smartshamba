<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ops_task_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('ops_tasks')->cascadeOnDelete();
            $table->string('assignment_type');
            $table->foreignId('worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role_on_task')->nullable();
            $table->string('status')->default('assigned');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'assignment_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ops_task_assignments');
    }
};
