<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop livestock_diseases table first (has foreign key to diseases)
        Schema::dropIfExists('livestock_diseases');
        // Drop diseases table
        Schema::dropIfExists('diseases');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed - tables and data are permanently deleted
        // If rollback is needed, you would need to recreate the tables and seed data
        throw new \Exception('Rollback of disease table removal is not supported');
    }
};
