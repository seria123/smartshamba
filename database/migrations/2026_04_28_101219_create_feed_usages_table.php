<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('livestock_id')->constrained('livestock')->onDelete('cascade');
            $table->decimal('quantity', 12, 2);
            $table->string('unit');
            $table->date('usage_date');
            $table->string('usage_type')->nullable(); // e.g., maintenance, growth, lactation
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['feed_type_id', 'livestock_id']);
            $table->index(['usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_usages');
    }
};
