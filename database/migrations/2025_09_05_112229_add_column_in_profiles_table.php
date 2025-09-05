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
        Schema::table('profiles', function (Blueprint $table) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->string('country')->nullable()->after('phone');
                $table->string('state')->nullable()->after('country');
                $table->string('city')->nullable()->after('state');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['country','state','city']);
        });
    }
};
