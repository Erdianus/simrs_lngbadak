<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subLayanans = [
            ['nama' => 'Kamar VIP', 'layanan_id' => 1],
            ['nama' => 'Kamar Kelas I', 'layanan_id' => 1],
            ['nama' => 'Kamar Kelas II', 'layanan_id' => 1],
            ['nama' => 'Kamar Kelas III', 'layanan_id' => 1],
            ['nama' => 'Poliklinik Umum', 'layanan_id' => 2],
            ['nama' => 'Poliklinik Spesialis', 'layanan_id' => 2],
            ['nama' => 'Pelayanan IGD', 'layanan_id' => 3],
        ];

        foreach ($subLayanans as $subLayanan) {
            \App\Models\SubLayanan::create($subLayanan);
        }
    }
}
