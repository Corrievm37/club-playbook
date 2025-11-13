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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            // Define columns first; add FKs in a separate migration to avoid ordering issues
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('relationship'); // parent, guardian, other
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('primary_contact')->default(false);
            $table->timestamps();
            $table->unique(['player_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
