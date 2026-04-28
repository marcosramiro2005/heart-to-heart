<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // forum_posts: filtros por categoría y ordenación por fecha
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->index('categoria');
            $table->index('created_at');
        });

        // emotional_records: consultas por usuario + rango de fechas
        Schema::table('emotional_records', function (Blueprint $table) {
            $table->index(['user_id', 'recorded_at']);
        });

        // diary_entries: listado por usuario ordenado por fecha
        Schema::table('diary_entries', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        // chat_messages: historial de Hearty por usuario en orden cronológico
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        // user_challenges: lookup por usuario + challenge (eliminaba el N+1)
        Schema::table('user_challenges', function (Blueprint $table) {
            $table->index(['user_id', 'challenge_id']);
            $table->index(['user_id', 'status']);
        });

        // resource_saves: lookup de guardados por usuario
        Schema::table('resource_saves', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropIndex(['categoria']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('emotional_records', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'recorded_at']);
        });

        Schema::table('diary_entries', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('user_challenges', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'challenge_id']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('resource_saves', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
