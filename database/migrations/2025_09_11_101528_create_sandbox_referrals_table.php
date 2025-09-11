<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sandbox_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // each user can appear only once
            $table->unsignedBigInteger('parent_id')->nullable(); // immediate parent in sandbox tree (refers to this table's id)
            $table->unsignedBigInteger('root_id')->nullable();   // top-level ancestor
            $table->string('serial')->unique();                 // e.g., SB25091115
            $table->integer('position')->default(1);           // 1–10 under parent
            $table->timestamps();

        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sandbox_referrals');
    }
};
