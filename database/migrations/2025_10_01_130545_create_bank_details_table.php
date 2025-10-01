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
        Schema::create('bank_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('bank_name')->nullable();
            $t->string('account_number')->nullable();
            $t->string('account_holder')->nullable();
            $t->timestamps();
        });

        Schema::table('collections', function (Blueprint $t) {
            $t->boolean('is_redeemed')->default(false)->after('pending_balance');
            
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_details');
    }
};
