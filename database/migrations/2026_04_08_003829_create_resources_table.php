<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir columnas que faltan a la tabla resources existente
        Schema::table('resources', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('content');
            $table->string('type')->default('article')->after('category');
            $table->string('external_url')->nullable()->after('image_url');
            $table->integer('read_time')->default(5)->after('external_url');
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->integer('views')->default(0)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['summary', 'type', 'external_url', 'read_time', 'is_featured', 'views']);
        });
    }
};