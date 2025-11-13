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
        Schema::table('players', function (Blueprint $table) {
            $table->enum('status', ['pending','approved','rejected'])->default('pending')->after('consent_guardian');
            $table->foreignId('registered_by_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->after('registered_by_user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['status','registered_by_user_id','approved_by_user_id','approved_at']);
        });
    }
};
