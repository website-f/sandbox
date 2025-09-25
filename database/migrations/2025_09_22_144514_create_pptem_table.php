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
        Schema::create('pptem', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->nullable();
            $table->string("pptem_number")->nullable();
            $table->string("pptem_ref")->nullable();
            $table->string("expire_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pptem');
    }
};
