<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_id')->nullable()->constrained('livestock')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('vaccine_name');
            $table->string('vaccine_type')->nullable();
            $table->date('vaccination_date');
            $table->date('next_due_date')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('status')->default('completed');
            $table->string('administrator')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_vaccinations');
    }
};