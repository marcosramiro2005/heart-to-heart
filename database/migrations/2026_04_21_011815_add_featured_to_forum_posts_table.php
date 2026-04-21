<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('forum_posts', function (Blueprint $table) {
        if (!Schema::hasColumn('forum_posts', 'is_featured')) {
            $table->boolean('is_featured')->default(false)->after('is_anonymous');
        }
        if (!Schema::hasColumn('forum_posts', 'views')) {
            $table->integer('views')->default(0)->after('is_featured');
        }
        if (!Schema::hasColumn('forum_posts', 'categoria')) {
            $table->string('categoria')->default('general')->after('views');
        }
    });
}
};
