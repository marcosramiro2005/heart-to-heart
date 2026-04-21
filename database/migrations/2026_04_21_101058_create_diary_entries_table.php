<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('diary_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->text('content');
        $table->string('mood')->nullable();
        $table->integer('mood_score')->default(5);
        $table->json('tags')->nullable();
        $table->boolean('is_private')->default(true);
        $table->string('weather')->nullable();
        $table->timestamps();
    });
}
};
