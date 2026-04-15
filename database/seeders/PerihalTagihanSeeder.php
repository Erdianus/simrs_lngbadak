<?php

namespace Database\Seeders;

use App\Models\Eslon;
use App\Models\Layanan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerihalTagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanan = Layanan::select('id')->get()->toArray();
        $perihalTagihan =
            [
                'kode' => '1',
                'hal' => 'Dokter',
                'hal' => 'Biaya Dokter Sp. Luar (On Call) RS PKT',
                'jenis_tagihan' => 'Dokter',
                'deskripsi' => 'Terlampir dikirimkan Dokumen Pendukung Pembayaran atas jasa konsultasi medis berupa Rincian Pembayaran Atas Tindakan Medis yang dilakukan oleh dokter Sp. tidak tetap.',
                'ket_pembayaran' => 'Biaya',
                'layanan_id' => $layanan[rand(0, count($layanan) - 1)]['id'],
                'status' => 'Anak Sekolah Luar Bontang',
                'unit_dokter' => 'dr. Adi Rizka, SpB',
                'kota' => 'Bontang',
                'rumah_sakit' => 'RS Amalia',
                'spesialisasi' => 'UGD',
                'bank' => 'ANZ PANIN BANK',
                'status' => 'PISA',
                'tindakan' => 'Jasa Dokter',
            ];
        \App\Models\PerihalTagihan::create($perihalTagihan);
    }
}
