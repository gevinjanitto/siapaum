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
        Schema::table('reklames', function (Blueprint $table) {
            $table->foreignId('permohonan_id')->nullable()->after('registrasi_reklame_id')->constrained('permohonans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reklames', function (Blueprint $table) {
            $table->dropForeign(['permohonan_id']);
            $table->dropColumn('permohonan_id');
        });
    }
};
