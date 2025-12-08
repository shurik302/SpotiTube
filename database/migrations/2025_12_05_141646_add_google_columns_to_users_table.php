<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('password')->index();
            $table->string('google_avatar')->nullable()->after('google_id');
            $table->string('google_token', 1024)->nullable()->after('google_avatar');
            $table->string('google_refresh_token', 1024)->nullable()->after('google_token');
            $table->boolean('password_set')->default(true)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'google_avatar', 'google_token', 'google_refresh_token', 'password_set']);
        });
    }
};
