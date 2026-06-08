<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('livestock_id')->nullable()->constrained('livestock')->nullOnDelete();
            $table->string('name');
            $table->string('budget_type')->default('seasonal');
            $table->string('category')->nullable();
            $table->decimal('planned_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('alert_threshold_percent', 5, 2)->default(100);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['farm_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
