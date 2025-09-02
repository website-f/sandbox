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
        Schema::create('accounts', function (Blueprint $t) {
          $t->id();
          $t->foreignId('user_id')->constrained()->cascadeOnDelete();
          $t->enum('type', ['rizqmall','sandbox']);
          $t->boolean('active')->default(false);
          $t->date('expires_at')->nullable();
          $t->timestamps();
          $t->unique(['user_id','type']);
        });
    
        Schema::create('subscriptions', function (Blueprint $t) {
          $t->id();
          $t->foreignId('user_id')->constrained()->cascadeOnDelete();
          $t->enum('plan', ['rizqmall','sandbox']);
          $t->unsignedInteger('amount'); // cents (e.g. 2000, 30000)
          $t->enum('status', ['pending','paid','expired','failed'])->default('pending');
          $t->dateTime('starts_at')->nullable();
          $t->dateTime('ends_at')->nullable();
          $t->string('provider')->default('toyyibpay');
          $t->string('provider_ref')->nullable(); // billCode
          $t->json('meta')->nullable();
          $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('accounts');
    }
};
