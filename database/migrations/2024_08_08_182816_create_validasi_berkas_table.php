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
        Schema::create('validasi_berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alur_permohonan_id')->constrained('alur_permohonans');
            $table->foreignId('berkas_permohonan_id')->constrained('berkas_permohonans');
            $table->string('status')->default('pending')->comment('revisi, valid');
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi_berkas');
    }
};
