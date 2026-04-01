<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('chat_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('sender', ['user', 'hearty']); // quién envió el mensaje
        $table->text('message');
        $table->string('emotion_detected')->nullable(); // emoción que detectó Hearty
        $table->timestamps();
    });
}
};
