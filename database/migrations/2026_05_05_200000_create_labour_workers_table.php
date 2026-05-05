<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_workers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('worker_code');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('employment_type');
            $table->string('primary_role');
            $table->string('status')->default('active');
            $table->date('start_date')->nullable();
            $table->string('rate_type')->nullable();
            $table->decimal('default_rate', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['farm_id', 'worker_code']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labour_workers');
    }
};
