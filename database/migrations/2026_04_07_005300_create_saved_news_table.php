<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('saved_news', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('article_url');
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('image_url')->nullable();
        $table->string('source_name')->nullable();
        $table->string('category')->nullable();
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
        $table->unique(['user_id', 'article_url']);
    });
}
};
