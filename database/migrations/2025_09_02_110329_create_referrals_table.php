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
        Schema::create('referrals', function (Blueprint $t) {
          $t->id();
          $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
          $t->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
          $t->foreignId('root_id')->nullable()->constrained('users')->nullOnDelete(); // top ancestor
          $t->unsignedTinyInteger('level')->default(1); // 1..7
          $t->unsignedTinyInteger('direct_children')->default(0); // convenience
          $t->string('ref_code')->unique(); // to share
          $t->timestamps();
          $t->index(['root_id','level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
