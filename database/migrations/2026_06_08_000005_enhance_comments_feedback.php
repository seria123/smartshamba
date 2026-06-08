<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (! Schema::hasColumn('comments', 'topic_tags')) {
                $table->json('topic_tags')->nullable()->after('body');
            }

            if (! Schema::hasColumn('comments', 'mentions')) {
                $table->json('mentions')->nullable()->after('topic_tags');
            }

            if (! Schema::hasColumn('comments', 'insight_type')) {
                $table->string('insight_type')->nullable()->after('mentions');
            }

            if (! Schema::hasColumn('comments', 'sentiment')) {
                $table->string('sentiment')->nullable()->after('insight_type');
            }

            if (! Schema::hasColumn('comments', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('sentiment');
            }

            if (! Schema::hasColumn('comments', 'voice_note_path')) {
                $table->string('voice_note_path')->nullable()->after('is_urgent');
            }

            if (! Schema::hasColumn('comments', 'board_type')) {
                $table->string('board_type')->default('community')->after('voice_note_path');
            }

            if (! Schema::hasColumn('comments', 'moderation_status')) {
                $table->string('moderation_status')->default('visible')->after('board_type');
            }

            if (! Schema::hasColumn('comments', 'converted_to_type')) {
                $table->string('converted_to_type')->nullable()->after('moderation_status');
            }

            if (! Schema::hasColumn('comments', 'converted_to_id')) {
                $table->unsignedBigInteger('converted_to_id')->nullable()->after('converted_to_type');
            }
        });

        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction_type');
            $table->timestamps();

            $table->unique(['comment_id', 'user_id', 'reaction_type']);
            $table->index(['comment_id', 'reaction_type']);
        });

        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
        Schema::dropIfExists('comment_reactions');

        Schema::table('comments', function (Blueprint $table) {
            foreach ([
                'topic_tags',
                'mentions',
                'insight_type',
                'sentiment',
                'is_urgent',
                'voice_note_path',
                'board_type',
                'moderation_status',
                'converted_to_type',
                'converted_to_id',
            ] as $column) {
                if (Schema::hasColumn('comments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
