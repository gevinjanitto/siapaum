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
        Schema::table('kuesioner_jawabans', function (Blueprint $table) {
            $table->foreignId('kuesioner_pertanyaan_id')->nullable()->constrained('kuesioner_pertanyaans');
            $table->string('pertanyaan')->nullable();
            $table->string('opsi')->nullable();
            $table->integer('point')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuesioner_jawabans', function (Blueprint $table) {
            $table->dropForeign(['kuesioner_pertanyaan_id']);
            $table->dropColumn('kuesioner_pertanyaan_id');
            $table->dropColumn('pertanyaan');
            $table->dropColumn('opsi');
            $table->dropColumn('point');
        });
    }
};
