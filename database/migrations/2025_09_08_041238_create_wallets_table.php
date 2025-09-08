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
        Schema::create('wallets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('balance')->default(0); // in cents (e.g. 300 = RM3.00)
            $t->timestamps();
        });
        
        Schema::create('wallet_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['credit','debit']);
            $t->unsignedBigInteger('amount'); // cents
            $t->string('description')->nullable();
            $t->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('wallet_transactions');
    }
};
