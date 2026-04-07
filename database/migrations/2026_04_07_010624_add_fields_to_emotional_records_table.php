<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('emotional_records', function (Blueprint $table) {
        $table->string('color')->nullable()->after('emotion'); // color asociado a la emoción
        $table->string('weather')->nullable()->after('notes'); // contexto clima
        $table->string('activity')->nullable()->after('weather'); // actividad del día
    });
}

public function down(): void
{
    Schema::table('emotional_records', function (Blueprint $table) {
        $table->dropColumn(['color', 'weather', 'activity']);
    });
}
};
