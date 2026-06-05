<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_field_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->date('assigned_date');
            $table->date('unassigned_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['staff_id', 'field_id']);
            $table->index('farm_id');
            $table->unique(['staff_id', 'field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_field_assignments');
    }
};
