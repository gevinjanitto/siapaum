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
            $table->string('skpd_filepath')->nullable()->after('image_filepath');
            $table->string('bukti_bayar_filepath')->nullable();
            $table->foreignId('bukti_bayar_user_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reklames', function (Blueprint $table) {
            $table->dropColumn('skpd_filepath');
            $table->dropColumn('bukti_bayar_filepath');
            $table->dropForeign(['bukti_bayar_user_id']);
            $table->dropColumn('bukti_bayar_user_id');
        });
    }
};
