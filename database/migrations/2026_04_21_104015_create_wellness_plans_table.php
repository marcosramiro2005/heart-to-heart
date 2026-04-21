<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('wellness_plans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('objetivo');
        $table->json('actividades');
        $table->date('semana_inicio');
        $table->integer('dias_completados')->default(0);
        $table->json('dias_check')->default('[]');
        $table->boolean('completado')->default(false);
        $table->timestamps();
    });
}
};
