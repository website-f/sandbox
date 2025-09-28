<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pewaris', function (Blueprint $table) {
            $table->string('dob')->nullable()->after('email');
        });

        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('rizqmall','sandbox','sandbox remaja') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
