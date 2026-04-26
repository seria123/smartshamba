<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE crops DROP INDEX crops_name_variety_unique');
        DB::statement('ALTER TABLE crops ADD CONSTRAINT crops_field_name_variety_unique UNIQUE (field_id, name, variety)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE crops DROP INDEX crops_field_name_variety_unique');
        DB::statement('ALTER TABLE crops ADD CONSTRAINT crops_name_variety_unique UNIQUE (name, variety)');
    }
};
