<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanans = [
            ['nama' => 'Rawat Inap'],
            ['nama' => 'Rawat Jalan'],
            ['nama' => 'MCU'],
            ['nama' => 'DCU'],
            ['nama' => 'SKD'],
            ['nama' => 'Pemeriksaan Narkoba'],
        ];

        foreach ($layanans as $layanan) {
            \App\Models\Layanan::create($layanan);
        }
    }
}
