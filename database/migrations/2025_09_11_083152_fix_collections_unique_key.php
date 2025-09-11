<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);

            // Drop the old unique key on user_id
            $table->dropUnique('collections_user_id_unique');

            // Add composite unique key (user_id + type)
            $table->unique(['user_id', 'type']);

            // Recreate foreign key after changing unique index
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);

            // Drop composite unique key
            $table->dropUnique(['user_id', 'type']);

            // Recreate old unique key
            $table->unique('user_id');

            // Recreate foreign key
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
