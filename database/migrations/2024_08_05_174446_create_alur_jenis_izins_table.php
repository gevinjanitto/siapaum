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
        Schema::create('alur_jenis_izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_izin_id')->constrained('jenis_izins');
            $table->foreignId('verifikator_id')->constrained('users');
            $table->string('jenis_verifikator')->nullable();
            $table->integer('urutan');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alur_jenis_izins');
    }
};
