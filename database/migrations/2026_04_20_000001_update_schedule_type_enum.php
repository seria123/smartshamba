<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE irrigation_zones MODIFY COLUMN schedule_type ENUM('manual', 'scheduled', 'smart', 'weekdays') DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE irrigation_zones MODIFY COLUMN schedule_type ENUM('manual', 'scheduled', 'smart') DEFAULT 'manual'");
    }
};
