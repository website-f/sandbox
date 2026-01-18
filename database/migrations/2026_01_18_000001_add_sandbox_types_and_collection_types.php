<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds support for multiple Sandbox account types:
     * - sandbox_usahawan (previously just 'sandbox')
     * - sandbox_remaja (for 11-20 year olds, registered by parent)
     * - sandbox_awam (general public)
     *
     * Each type has its own set of collections/tabung
     */
    public function up(): void
    {
        // Create collection_types table to define all tabung types and their limits
        Schema::create('collection_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 'geran_asas', 'biasiswa_pemula'
            $table->string('name'); // Display name e.g., 'Geran Asas'
            $table->string('account_type'); // Which sandbox type this belongs to: sandbox_usahawan, sandbox_remaja, sandbox_awam
            $table->unsignedBigInteger('limit')->nullable(); // Maximum limit in cents (null = unlimited)
            $table->unsignedBigInteger('target')->nullable(); // Target amount in cents (for progress tracking like Geran Asas)
            $table->boolean('is_starter')->default(false); // Is this the starter tabung (like Geran Asas)?
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add collection_type_id to collections table
        Schema::table('collections', function (Blueprint $table) {
            if (!Schema::hasColumn('collections', 'collection_type_id')) {
                $table->foreignId('collection_type_id')->nullable()->after('type')->constrained('collection_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('collections', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('collection_type_id');
            }
        });

        // Add account_subtype to accounts table to differentiate sandbox types
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'subtype')) {
                $table->string('subtype')->nullable()->after('type'); // usahawan, remaja, awam
            }
        });

        // Update account_types enum to support new types
        // Note: The Account model's type column already supports 'sandbox remaja'
        // We'll add 'sandbox awam' and rename 'sandbox' to be clearer
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            if (Schema::hasColumn('collections', 'collection_type_id')) {
                $table->dropForeign(['collection_type_id']);
                $table->dropColumn('collection_type_id');
            }
            if (Schema::hasColumn('collections', 'serial_number')) {
                $table->dropColumn('serial_number');
            }
        });

        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'subtype')) {
                $table->dropColumn('subtype');
            }
        });

        Schema::dropIfExists('collection_types');
    }
};
