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
        Schema::create('kuesioner_opsis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuesioner_pertanyaan_id')->constrained('kuesioner_pertanyaans');
            $table->string('opsi');
            $table->integer('point');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuesioner_opsis');
    }
};
