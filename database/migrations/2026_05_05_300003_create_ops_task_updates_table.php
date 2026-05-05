<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ops_task_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('ops_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->string('update_type')->default('comment');
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->decimal('quantity_done', 12, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'update_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ops_task_updates');
    }
};
