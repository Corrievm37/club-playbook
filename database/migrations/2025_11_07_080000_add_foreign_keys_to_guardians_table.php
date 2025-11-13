<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            // Ensure indexes exist for FK columns (MySQL adds them automatically for foreign())
            $table->index('player_id');
            $table->index('user_id');

            $table->foreign('player_id')
                ->references('id')->on('players')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['player_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
