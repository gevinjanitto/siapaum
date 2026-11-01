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
            $table->boolean('is_pas_foto_required')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_izins', function (Blueprint $table) {
            $table->dropColumn('is_pas_foto_required');
        });
    }
};
