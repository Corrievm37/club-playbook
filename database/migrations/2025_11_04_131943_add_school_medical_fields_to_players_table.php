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
            $table->string('school_name')->nullable()->after('sa_id_number');
            $table->string('medical_aid_name')->nullable()->after('school_name');
            $table->string('medical_aid_number')->nullable()->after('medical_aid_name');
            $table->string('id_document_path')->nullable()->after('medical_aid_number');
            $table->string('medical_aid_card_path')->nullable()->after('id_document_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['school_name','medical_aid_name','medical_aid_number','id_document_path','medical_aid_card_path']);
            // cannot revert sa_id_number nullability safely here without data loss
        });
    }
};
