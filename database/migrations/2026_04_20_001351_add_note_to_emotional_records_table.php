<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('emotional_records', function (Blueprint $table) {
        if (!Schema::hasColumn('emotional_records', 'note')) {
            $table->text('note')->nullable()->after('intensity');
        }
        if (!Schema::hasColumn('emotional_records', 'triggers')) {
            $table->string('triggers')->nullable()->after('note');
        }
        if (!Schema::hasColumn('emotional_records', 'activities')) {
            $table->string('activities')->nullable()->after('triggers');
        }
    });
}

public function down(): void
{
    Schema::table('emotional_records', function (Blueprint $table) {
        $table->dropColumn(['note', 'triggers', 'activities']);
    });
}
};
