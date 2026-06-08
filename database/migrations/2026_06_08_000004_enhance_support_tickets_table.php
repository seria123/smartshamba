<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE support_tickets MODIFY priority ENUM('low', 'normal', 'medium', 'high', 'urgent') DEFAULT 'medium'");
        DB::statement("ALTER TABLE support_tickets MODIFY category ENUM('technical', 'billing', 'account', 'farm_management', 'livestock', 'crop', 'finance', 'system_bug', 'feature_request', 'other') DEFAULT 'other'");

        Schema::table('support_tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('support_tickets', 'support_channel')) {
                $table->string('support_channel')->default('in_app')->after('category');
            }

            if (! Schema::hasColumn('support_tickets', 'auto_tags')) {
                $table->json('auto_tags')->nullable()->after('support_channel');
            }

            if (! Schema::hasColumn('support_tickets', 'suggested_solutions')) {
                $table->json('suggested_solutions')->nullable()->after('auto_tags');
            }

            if (! Schema::hasColumn('support_tickets', 'context_snapshot')) {
                $table->json('context_snapshot')->nullable()->after('suggested_solutions');
            }

            if (! Schema::hasColumn('support_tickets', 'media_paths')) {
                $table->json('media_paths')->nullable()->after('context_snapshot');
            }

            if (! Schema::hasColumn('support_tickets', 'assigned_role')) {
                $table->string('assigned_role')->nullable()->after('media_paths');
            }

            if (! Schema::hasColumn('support_tickets', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('assigned_role')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('support_tickets', 'first_response_at')) {
                $table->timestamp('first_response_at')->nullable()->after('assigned_to');
            }

            if (! Schema::hasColumn('support_tickets', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('first_response_at');
            }

            if (! Schema::hasColumn('support_tickets', 'sla_due_at')) {
                $table->timestamp('sla_due_at')->nullable()->after('resolved_at');
            }

            if (! Schema::hasColumn('support_tickets', 'satisfaction_rating')) {
                $table->unsignedTinyInteger('satisfaction_rating')->nullable()->after('closed_at');
            }

            if (! Schema::hasColumn('support_tickets', 'satisfaction_comment')) {
                $table->text('satisfaction_comment')->nullable()->after('satisfaction_rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'assigned_to')) {
                $table->dropConstrainedForeignId('assigned_to');
            }

            foreach ([
                'support_channel',
                'auto_tags',
                'suggested_solutions',
                'context_snapshot',
                'media_paths',
                'assigned_role',
                'first_response_at',
                'resolved_at',
                'sla_due_at',
                'satisfaction_rating',
                'satisfaction_comment',
            ] as $column) {
                if (Schema::hasColumn('support_tickets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
