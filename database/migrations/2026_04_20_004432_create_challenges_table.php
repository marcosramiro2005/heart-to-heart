<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('challenges', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->string('type'); // '7days' o '30days'
        $table->string('category'); // ansiedad, mindfulness, etc
        $table->integer('duration_days');
        $table->string('emoji');
        $table->string('color');
        $table->timestamps();
    });

    Schema::create('user_challenges', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
        $table->date('started_at');
        $table->date('completed_at')->nullable();
        $table->integer('current_day')->default(1);
        $table->json('completed_days')->default('[]');
        $table->string('status')->default('active'); // active, completed, abandoned
        $table->timestamps();
        $table->unique(['user_id', 'challenge_id']);
    });
}
};
