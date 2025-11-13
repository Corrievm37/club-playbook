<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('team_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_session_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('player_id');
            $table->timestamps();

            $table->unique(['attendance_session_id', 'player_id']);
            $table->index(['attendance_session_id']);
            $table->index(['team_id']);
            $table->index(['player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_assignments');
    }
};
