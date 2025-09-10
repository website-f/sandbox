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
        Schema::create('collections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('balance')->default(0); // in cents (e.g. 30000 = RM300.00)
            $t->timestamps();
        });
        
        Schema::create('collection_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('collection_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('collection');
        Schema::dropIfExists('collection_transactions');
    }
};
