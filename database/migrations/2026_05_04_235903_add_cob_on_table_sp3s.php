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
        Schema::table('sp3s', function (Blueprint $table) {
            $table->string('jenis_sp3')->nullable();
            $table->integer('kunjungan')->nullable();
            $table->integer('pasien')->nullable();
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->integer('cob')->nullable();
            $table->string('no_rm')->nullable();
            $table->integer('biaya')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sp3s', function (Blueprint $table) {
            $table->dropColumn(['jenis_sp3', 'kunjungan', 'pasien']);
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn(['cob', 'no_rm', 'biaya']);
        });
    }
};
