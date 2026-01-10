<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // mode: 'light', 'dark', 'system'
            $table->string('theme_mode')->default('system')->after('password');
            // color: 'teal', 'purple', 'blue', 'green', 'red', 'yellow', 'orange', 'pink'
            $table->string('theme_color')->default('teal')->after('theme_mode');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['theme_mode', 'theme_color']);
        });
    }
};