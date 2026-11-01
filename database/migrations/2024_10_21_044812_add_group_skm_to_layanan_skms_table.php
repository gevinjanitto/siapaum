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
        Schema::table('layanan_skms', function (Blueprint $table) {
            $table->foreignId('group_layanan_skm_id')->nullable()->constrained('group_layanan_skms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanan_skms', function (Blueprint $table) {
            $table->dropForeign(['group_layanan_skm_id']);
            $table->dropColumn('group_layanan_skm_id');
        });
    }
};
