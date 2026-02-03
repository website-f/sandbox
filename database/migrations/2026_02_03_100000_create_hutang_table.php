<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Hutang (Debt) tracking for Sandbox Awam accounts
     * - Tracks debts from BEFORE registration date
     * - Total cannot exceed RM500,000
     */
    public function up(): void
    {
        Schema::create('hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('hutang_date'); // Must be before user's registration date
            $table->string('reference')->nullable(); // Reference number/code
            $table->text('description'); // Description of the debt
            $table->unsignedBigInteger('amount'); // Amount in cents (sen)
            $table->boolean('is_settled')->default(false); // Whether debt is settled/paid
            $table->date('settled_date')->nullable(); // When it was settled
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            // Index for faster queries
            $table->index(['user_id', 'is_settled']);
            $table->index(['user_id', 'hutang_date']);
        });

        // Separate table for multiple document uploads
        Schema::create('hutang_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hutang_id')->constrained('hutang')->onDelete('cascade');
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Storage path
            $table->string('file_type')->nullable(); // MIME type
            $table->unsignedInteger('file_size')->nullable(); // Size in bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_documents');
        Schema::dropIfExists('hutang');
    }
};
