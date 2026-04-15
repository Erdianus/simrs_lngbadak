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
        Schema::create('perihal_tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('hal');
            $table->string('jenis_tagihan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('ket_pembayaran')->nullable();
            $table->foreignId('layanan_id')->nullable()->constrained('layanans');
            $table->string('status')->nullable();
            $table->string('unit_dokter')->nullable();
            $table->string('kota')->nullable();
            $table->string('rumah_sakit')->nullable();
            $table->string('spesialisasi')->nullable();
            $table->string('bank')->nullable();
            $table->foreignId('eslon_id')->nullable()->constrained('eslons');
            $table->string('tindakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perihal_tagihans');
    }
};
