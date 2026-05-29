<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_fumigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('procedure_type');
            $table->string('chemical_used');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->default('liters');
            $table->date('procedure_date');
            $table->date('next_schedule_date')->nullable();
            $table->string('status')->default('completed');
            $table->string('administrator')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_fumigations');
    }
};