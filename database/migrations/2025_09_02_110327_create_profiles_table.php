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
       Schema::create('profiles', function (Blueprint $t) {
         $t->id();
         $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
         $t->string('full_name')->nullable();
         $t->string('nric')->nullable();
         $t->date('dob')->nullable();
         $t->text('home_address')->nullable();
         $t->string('phone')->nullable();
         $t->string('email_alt')->nullable();
         $t->string('photo_path')->nullable();
         $t->timestamps();
       });
   
       Schema::create('businesses', function (Blueprint $t) {
         $t->id();
         $t->foreignId('user_id')->constrained()->cascadeOnDelete();
         $t->string('company_name')->nullable();
         $t->string('ssm_no')->nullable();
         $t->text('business_address')->nullable();
         $t->string('industry')->nullable();
         $t->string('main_products_services')->nullable();
         $t->string('business_model')->nullable(); // B2B/B2C/Online/Offline
         $t->text('achievements')->nullable();
         $t->timestamps();
       });
   
       Schema::create('educations', function (Blueprint $t) {
         $t->id();
         $t->foreignId('user_id')->constrained()->cascadeOnDelete();
         $t->string('primary')->nullable();
         $t->string('secondary')->nullable();
         $t->string('higher')->nullable();
         $t->string('skills_training')->nullable();
         $t->timestamps();
       });
   
       Schema::create('courses', function (Blueprint $t) {
         $t->id();
         $t->foreignId('user_id')->constrained()->cascadeOnDelete();
         $t->string('title')->nullable();           // e.g. INSKEN Basic Entrepreneurship
         $t->string('provider')->nullable(); // INSKEN/SME Corp/etc.
         $t->string('year')->nullable();
         $t->timestamps();
       });
   
       Schema::create('next_of_kins', function (Blueprint $t) {
         $t->id();
         $t->foreignId('user_id')->constrained()->cascadeOnDelete();
         $t->string('name')->nullable();
         $t->string('relationship')->nullable();
         $t->string('phone')->nullable();
         $t->text('address')->nullable();
         $t->timestamps();
       });
   
       Schema::create('affiliations', function (Blueprint $t) {
         $t->id();
         $t->foreignId('user_id')->constrained()->cascadeOnDelete();
         $t->string('organization')->nullable();
         $t->string('position')->nullable();
         $t->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliations');
        Schema::dropIfExists('next_of_kins');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('educations');
        Schema::dropIfExists('businesses');
        Schema::dropIfExists('profiles');
    }
};
