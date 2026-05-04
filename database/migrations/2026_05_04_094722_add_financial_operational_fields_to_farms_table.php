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
        Schema::table('farms', function (Blueprint $table) {
            $table->decimal('estimated_budget', 15, 2)->nullable()->after('storage_facilities');
            $table->enum('main_purpose', ['commercial', 'subsistence'])->nullable()->after('estimated_budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn(['estimated_budget', 'main_purpose']);
        });
    }
};
