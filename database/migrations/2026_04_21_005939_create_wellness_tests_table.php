<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('wellness_tests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->integer('puntuacion');
        $table->string('nivel'); // minimo, leve, moderado, moderado_severo, severo
        $table->json('respuestas');
        $table->json('recomendaciones');
        $table->timestamps();
    });
}
};
