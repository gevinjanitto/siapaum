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
        Schema::create('berkas_jenis_izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_izin_id')->constrained('jenis_izins');
            $table->string('nama');
            $table->boolean('is_required')->default(false);
            $table->integer('urutan')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_jenis_izins');
    }
};
