<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('club_id');
                $table->string('age_group');
                $table->string('name');
                $table->timestamps();
                $table->unique(['club_id','age_group','name']);
                $table->index(['club_id','age_group']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
