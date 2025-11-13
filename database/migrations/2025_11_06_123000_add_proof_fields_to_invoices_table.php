<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('proof_path')->nullable()->after('pdf_path');
            $table->timestamp('proof_uploaded_at')->nullable()->after('proof_path');
            $table->unsignedBigInteger('proof_uploaded_by')->nullable()->after('proof_uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['proof_path','proof_uploaded_at','proof_uploaded_by']);
        });
    }
};
