<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('achievements', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();       // identificador único
        $table->string('name');
        $table->text('description');
        $table->string('emoji');
        $table->string('color')->default('#4ECDC4');
        $table->string('category');             // racha, bienestar, social, explorador
        $table->integer('points')->default(10);
        $table->timestamps();
    });
}
};
