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
        Schema::table('kuesioners', function (Blueprint $table) {
            $table->foreignId('layanan_skm_id')->nullable()->constrained('layanan_skms');
            $table->foreignId('jenis_izin_id')->nullable()->constrained('jenis_izins');
            $table->boolean('is_from_skm')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            $table->dropForeign(['layanan_skm_id']);
            $table->dropColumn('layanan_skm_id');
            $table->dropForeign(['jenis_izin_id']);
            $table->dropColumn('jenis_izin_id');
            $table->dropColumn('is_from_skm');
        });
    }
};
