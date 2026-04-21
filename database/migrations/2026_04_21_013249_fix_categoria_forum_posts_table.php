<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('forum_posts', function (Blueprint $table) {
        // Añadir categoria con valor por defecto para que no falle
        if (!Schema::hasColumn('forum_posts', 'categoria')) {
            $table->string('categoria')->default('general')->after('content');
        }
    });
}
};
