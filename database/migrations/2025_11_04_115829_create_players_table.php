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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('sa_id_number')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->string('age_group'); // U6..U19
            $table->unsignedSmallInteger('season_year');
            $table->string('position_primary')->nullable();
            $table->string('position_secondary')->nullable();
            $table->string('medical_conditions')->nullable();
            $table->string('allergies')->nullable();
            $table->boolean('consent_guardian')->default(false);
            $table->timestamps();
            $table->index(['club_id','age_group','season_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
