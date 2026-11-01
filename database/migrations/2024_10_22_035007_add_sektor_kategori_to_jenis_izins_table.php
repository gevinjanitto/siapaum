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
        Schema::table('jenis_izins', function (Blueprint $table) {
            $table->foreignId('sektor_izin_id')->nullable()->constrained();
            $table->foreignId('kategori_izin_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_izins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sektor_izin_id');
            $table->dropConstrainedForeignId('kategori_izin_id');
        });
    }
};
