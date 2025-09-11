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
        Schema::table('collections', function (Blueprint $t) {
            $t->string('type')->after('user_id');
            $t->unsignedBigInteger('limit')->nullable()->after("balance");
            $t->unsignedBigInteger('pending_balance')->default(0)->after('balance'); // for Geran Asas progress
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $t) {
            //
        });
    }
};
