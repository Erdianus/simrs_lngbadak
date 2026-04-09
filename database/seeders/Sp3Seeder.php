<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Sp3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sp3s = [
            ['no_sp3' => 'SP3001', 'keterangan' => 'SP3 1'],
            ['no_sp3' => 'SP3002', 'keterangan' => 'SP3 2'],
            ['no_sp3' => 'SP3003', 'keterangan' => 'SP3 3'],
        ];

        foreach ($sp3s as $sp3) {
            \App\Models\Sp3::create($sp3);
        }

    }
}
