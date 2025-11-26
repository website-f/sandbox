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
            // RizqMall integration fields
            $table->timestamp('rizqmall_activated_at')->nullable()->after('remember_token');
            $table->integer('rizqmall_stores_quota')->default(0)->after('rizqmall_activated_at');
            $table->timestamp('last_rizqmall_sync')->nullable()->after('rizqmall_stores_quota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rizqmall_activated_at', 'rizqmall_stores_quota', 'last_rizqmall_sync']);
        });
    }
};
