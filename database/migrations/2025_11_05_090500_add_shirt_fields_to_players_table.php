<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('shirt_size')->nullable()->after('gender');
            $table->boolean('shirt_handed_out')->default(false)->after('shirt_size');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['shirt_size', 'shirt_handed_out']);
        });
    }
};
