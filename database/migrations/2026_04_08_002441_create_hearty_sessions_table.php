<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('hearty_sessions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('dominant_emotion')->nullable();   // emoción predominante detectada
        $table->float('avg_intensity')->nullable();       // intensidad media de la sesión
        $table->json('emotions_history')->nullable();     // historial de emociones de sesiones anteriores
        $table->json('suggested_techniques')->nullable(); // técnicas ya sugeridas
        $table->integer('session_count')->default(1);     // número de sesiones totales
        $table->text('last_summary')->nullable();         // resumen de la última sesión
        $table->timestamp('last_session_at')->nullable();
        $table->timestamps();
        $table->unique('user_id'); // una fila por usuario, se actualiza
    });
}
};
