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
            $table->date('tgl_sp3')->nullable();
            $table->string('jenis_surat')->nullable();
            $table->string('nomor_tagihan')->nullable();
            $table->date('tgl_terima_keu')->nullable();
            $table->foreignId('perihal_tagihan_id')->nullable()->constrained('perihal_tagihans');
            $table->string('ket_inv_pasien')->nullable()->comment('Nama pasiennya atau nama pasien dari PT(perusahaan) atau asuransi');
            $table->string('ket_inv_rs')->nullable()->comment('Keterangan invoice rumah sakit mana');
            $table->string('ket_pembayaran')->nullable();
            $table->foreignId('layanan_id')->nullable()->constrained('layanans');
            $table->string('kota')->nullable();
            $table->string('nama_rs')->nullable()->default('RS LNG BADAK');
            $table->string('dokter_rujukan')->nullable()->default('RS LNG BADAK');
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->integer('total_tagihan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sp3s', function (Blueprint $table) {
            $table->dropColumn('tgl_sp3');
            $table->dropColumn('jenis_surat');
            $table->dropColumn('nomor_tagihan');
            $table->dropColumn('tgl_terima_keu');
            $table->dropForeign(['perihal_tagihan_id']);
            $table->dropColumn('perihal_tagihan_id');
            $table->dropColumn('jumlah_pasien');
            $table->dropColumn('jumlah_kunjungan');
            $table->dropColumn('ket_pembayaran');
            $table->dropForeign(['layanan_id']);
            $table->dropColumn('layanan_id');
            $table->dropColumn('kota');
            $table->dropColumn('nama_rs');
            $table->dropColumn('dokter_rujukan');
            $table->dropColumn('tgl_masuk');
            $table->dropColumn('tgl_keluar');
            $table->dropColumn('total_tagihan');
        });
    }
};
