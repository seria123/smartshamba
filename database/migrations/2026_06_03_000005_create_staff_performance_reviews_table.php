<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->date('review_period_start');
            $table->date('review_period_end');
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_assigned')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->decimal('work_quality_score', 5, 2)->nullable();
            $table->decimal('efficiency_rating', 5, 2)->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->enum('rating', ['excellent', 'good', 'average', 'poor', 'very_poor'])->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['staff_id', 'review_period_start']);
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_performance_reviews');
    }
};
