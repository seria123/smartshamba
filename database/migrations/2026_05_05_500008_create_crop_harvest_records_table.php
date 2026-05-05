<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_harvest_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->foreignId('crop_activity_id')->nullable()->constrained('crop_activities')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->date('harvest_date');
            $table->decimal('quantity', 12, 2);
            $table->string('unit');
            $table->string('grade')->nullable();
            $table->string('destination')->nullable();
            $table->foreignId('harvested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_harvest_records');
    }
};
