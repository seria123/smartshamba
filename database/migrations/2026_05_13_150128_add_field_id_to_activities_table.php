<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null')->after('crop_cycle_id');
            $table->index(['field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['field_id']);
            $table->dropColumn(['field_id']);
            $table->dropIndex(['field_id']);
        });
    }
};
