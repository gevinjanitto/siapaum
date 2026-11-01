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
        Schema::table('reklames', function (Blueprint $table) {
            $table->boolean('is_bongkar')->default(false)->after('bukti_bayar_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reklames', function (Blueprint $table) {
            $table->dropColumn('is_bongkar');
        });
    }
};
