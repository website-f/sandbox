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
        Schema::table('collection_transactions', function (Blueprint $table) {
            $table->string('slip_path')->nullable()->after('description');
            $table->timestamp('transaction_date')->nullable()->after('slip_path');
            $table->text('admin_notes')->nullable()->after('transaction_date');
            $table->unsignedBigInteger('created_by')->nullable()->after('admin_notes');
            
            // Add foreign key for admin who created the transaction
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collection_transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['slip_path', 'transaction_date', 'admin_notes', 'created_by']);
        });
    }
};
