<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds dob (date of birth) and ic_number fields to pewaris table
     * for age validation when registering Sandbox Remaja accounts (11-20 years)
     */
    public function up(): void
    {
        Schema::table('pewaris', function (Blueprint $table) {
            if (!Schema::hasColumn('pewaris', 'dob')) {
                $table->date('dob')->nullable()->after('email');
            }
            if (!Schema::hasColumn('pewaris', 'ic_number')) {
                $table->string('ic_number')->nullable()->after('dob');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pewaris', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('pewaris', 'dob')) {
                $columns[] = 'dob';
            }
            if (Schema::hasColumn('pewaris', 'ic_number')) {
                $columns[] = 'ic_number';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
