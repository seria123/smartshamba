<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'title')) {
                $table->string('title')->nullable()->after('livestock_id');
            }
        });

        if (Schema::hasColumn('expenses', 'title') && Schema::hasColumn('expenses', 'description')) {
            DB::table('expenses')
                ->whereNull('title')
                ->update(['title' => DB::raw('description')]);
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
