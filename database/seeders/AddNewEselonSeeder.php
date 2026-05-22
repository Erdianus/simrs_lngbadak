<?php

namespace Database\Seeders;

use App\Models\Eslon;
use App\Models\Layanan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddNewEselonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eselons = [
            ['kode_eselon' => 'PRDWH', 'deskripsi' => 'PT. PRODIA WIDYAHUSADA TBK'],
            ['kode_eselon' => 'SEKMC', 'deskripsi' => 'SEKATA MEDICAL CENTER'],
            ['kode_eselon' => 'PMI', 'deskripsi' => 'PALANG MERAH INDONESIA'],
            // ['kode_eselon' => 'Eselon III', 'deskripsi' => 'Eselon III'],
            // ['kode_eselon' => 'Eselon IV', 'deskripsi' => 'Eselon IV']
        ];

        $layanans =
            [
                'Laboratorium',
                'Radiologi'
            ];

        foreach ($eselons as $eslon) {
            Eslon::create([
                'nama' => $eslon['kode_eselon'],
                'deskripsi' => $eslon['deskripsi'],
            ]);
        }

        foreach ($layanans as $layanan) {
            Layanan::create([
                'nama' => $layanan,
            ]);
        }
    }
}
