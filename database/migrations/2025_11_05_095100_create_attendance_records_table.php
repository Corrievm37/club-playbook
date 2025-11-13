<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_session_id');
            $table->unsignedBigInteger('player_id');
            $table->enum('rsvp_status', ['unknown','yes','no','maybe'])->default('unknown');
            $table->boolean('present')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['attendance_session_id','player_id']);
            $table->index(['attendance_session_id']);
            $table->index(['player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
