<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('forum_posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('content');
        $table->string('category'); // ansiedad, depresion, relaciones, autoestima, general
        $table->boolean('is_anonymous')->default(false);
        $table->integer('likes_count')->default(0);
        $table->integer('comments_count')->default(0);
        $table->boolean('is_pinned')->default(false);
        $table->timestamps();
    });
}
};
