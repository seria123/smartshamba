<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('labour_worker_id')->constrained('labour_workers')->cascadeOnDelete();
            $table->foreignId('labour_team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->date('date');
            $table->string('status');
            $table->time('check_in_at')->nullable();
            $table->time('check_out_at')->nullable();
            $table->decimal('hours_worked', 8, 2)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['labour_worker_id', 'date']);
            $table->index(['organization_id', 'farm_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labour_attendance_records');
    }
};
