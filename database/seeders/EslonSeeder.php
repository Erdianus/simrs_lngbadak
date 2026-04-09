<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EslonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eslons = [
            ['nama' => 'Mandiri Inhealth'],
            ['nama' => 'PISA'],
            ['nama' => 'BPJS Kesehatan'],
        ];

        foreach ($eslons as $eslon) {
            \App\Models\Eslon::create($eslon);
        }
    }
}
