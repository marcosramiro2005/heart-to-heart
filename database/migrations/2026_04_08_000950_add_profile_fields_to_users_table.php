<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('avatar')->nullable()->after('name');       // emoji o inicial
        $table->string('bio')->nullable()->after('avatar');        // descripción personal
        $table->string('location')->nullable()->after('bio');      // ciudad opcional
        $table->date('birth_date')->nullable()->after('location'); // fecha de nacimiento
        $table->boolean('profile_public')->default(true)->after('birth_date');
        $table->boolean('show_in_forum')->default(true)->after('profile_public');
        $table->string('theme_color')->default('#4ECDC4')->after('show_in_forum');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'avatar', 'bio', 'location',
            'birth_date', 'profile_public',
            'show_in_forum', 'theme_color'
        ]);
    });
}
};
