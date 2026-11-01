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
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('jenis_izin_id')->constrained('jenis_izins');
            $table->string('nama_jenis_izin');
            $table->text('deskripsi_jenis_izin');
            $table->string('nomor_registrasi')->nullable();
            $table->string('surat_kuasa_filepath')->nullable();
            $table->string('nama')->nullable();
            $table->string('nik')->nullable();
            $table->string('npwp')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('surat_permohonan_rekomendasi_filepath')->nullable();
            $table->string('surat_rekomendasi_filepath')->nullable();
            $table->string('template_surat_filepath')->nullable();
            $table->boolean('is_ttd')->default(false);
            $table->string('status')->default('pending')->comment('pending,permohonan_baru,selesai,revisi,verifikasi_ulang,expired,verifikasi');
            $table->boolean('is_expired')->default(false)->comment('Status permohonan yang revisi lama bisa expired');
            $table->boolean('is_legacy_data')->default(false);
            $table->timestamp('pengajuan_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
