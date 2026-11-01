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
        Schema::create('reklames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_reklame_id')->constrained('registrasi_reklames');
            $table->string('image_filepath')->nullable();
            $table->boolean('is_from_sireko')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reklames');
    }
};
