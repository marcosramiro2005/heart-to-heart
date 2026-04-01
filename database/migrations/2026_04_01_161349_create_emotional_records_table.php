<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('emotional_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('emotion');         // alegría, tristeza, ansiedad, calma, etc.
        $table->tinyInteger('intensity');  // del 1 al 10
        $table->text('notes')->nullable(); // nota opcional del usuario
        $table->date('recorded_at');       // fecha del registro
        $table->timestamps();
    });
}
};
