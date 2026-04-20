<?php

namespace Database\Seeders;

use App\Models\Eslon;
use App\Models\Simrs\EselonSimrs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EslonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eslons = EselonSimrs::select(['kode_eselon', 'deskripsi'])->get();

        foreach ($eslons as $eslon) {
            Eslon::create([
                'nama' => $eslon->kode_eselon,
                'deskripsi' => $eslon->deskripsi,
            ]);
        }
    }
}
