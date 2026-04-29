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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sp3_id')->references('id')->on('sp3s')->onDelete('cascade');
            $table->string('keterangan')->nullable();
            $table->string('no_registrasi')->unique();
            $table->string('nama_pasien');
            $table->foreignId('eslon_id')->references('id')->on('eslons');
            $table->foreignId('layanan_id')->nullable();
            $table->foreignId('sub_layanan_id')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar');
            $table->boolean('is_verified_by_verifikator')->default(false);
            $table->boolean('is_verified_by_keuangan')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
