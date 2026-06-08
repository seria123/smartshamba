<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->string('lender_name');
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('amount_repaid', 12, 2)->default(0);
            $table->date('loan_date');
            $table->date('due_date')->nullable();
            $table->string('repayment_frequency')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['farm_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
