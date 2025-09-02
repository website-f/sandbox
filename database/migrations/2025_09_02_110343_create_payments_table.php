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
        Schema::create('payments', function (Blueprint $t) {
          $t->id();
          $t->foreignId('subscription_id')->constrained()->cascadeOnDelete();
          $t->string('provider')->default('toyyibpay');
          $t->string('bill_code')->nullable();
          $t->string('status')->default('pending'); // pending, success, failed
          $t->unsignedInteger('amount');
          $t->dateTime('paid_at')->nullable();
          $t->json('payload')->nullable(); // callback/return payload
          $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
