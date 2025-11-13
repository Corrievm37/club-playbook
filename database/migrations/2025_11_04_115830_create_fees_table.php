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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->unsignedSmallInteger('season_year');
            $table->string('name'); // e.g., Registration 2026 U13
            $table->unsignedInteger('amount_cents'); // ZAR in cents
            $table->date('due_date')->nullable();
            $table->json('installment_plan')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['club_id','season_year','name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
