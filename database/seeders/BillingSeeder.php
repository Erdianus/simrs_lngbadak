<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billings = [
            ['sp3_id' => 1, 'no_registrasi' => 'REG001', 'keterangan' => 'Billing 1', 'eslon_id' => 1, 'layanan_id' => 1, 'sub_layanan_id' => 1,'tanggal_masuk' => '2023-01-01','tanggal_keluar' => '2023-01-05', 'biaya' => 1000000],
            ['sp3_id' => 1, 'no_registrasi' => 'REG002', 'keterangan' => 'Billing 1', 'eslon_id' => 1, 'layanan_id' => 1, 'sub_layanan_id' => 1,'tanggal_masuk' => '2023-01-01','tanggal_keluar' => '2023-01-05', 'biaya' => 1000000],
            ['sp3_id' => 2, 'no_registrasi' => 'REG003', 'keterangan' => 'Billing 2', 'eslon_id' => 2, 'layanan_id' => 2, 'sub_layanan_id' => 5, 'tanggal_masuk' => '2023-01-02', 'tanggal_keluar' => '2023-01-06', 'biaya' => 500000],
            ['sp3_id' => 3, 'no_registrasi' => 'REG004', 'keterangan' => 'Billing 3', 'eslon_id' => 3, 'layanan_id' => 3, 'sub_layanan_id' => 7, 'tanggal_masuk' => '2023-01-03', 'tanggal_keluar' => '2023-01-07', 'biaya' => 200000],
            ['sp3_id' => null, 'no_registrasi' => 'REG005', 'keterangan' => 'Billing 3', 'eslon_id' => 3, 'layanan_id' => 3, 'sub_layanan_id' => 7, 'tanggal_masuk' => '2023-01-03', 'tanggal_keluar' => '2023-01-07', 'biaya' => 200000],
        ];

        foreach ($billings as $billing) {
            \App\Models\Billing::create($billing);
        }
    }
}
