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
        Schema::create('roles', function (Blueprint $t) {
          $t->id();
          $t->string('name')->unique(); // Admin, Entrepreneur
          $t->timestamps();
        });
    
        Schema::create('role_user', function (Blueprint $t) {
          $t->id();
          $t->foreignId('user_id')->constrained()->cascadeOnDelete();
          $t->foreignId('role_id')->constrained()->cascadeOnDelete();
          $t->unique(['user_id','role_id']);
          $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('role_user');
         Schema::dropIfExists('roles');
    }
};
