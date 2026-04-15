<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerihalTagihan extends Model
{
    use HasFactory;

    protected $table = 'perihal_tagihans';

    protected $fillable = [
        'kode',
        'hal',
        'jenis_tagihan',
        'deksripsi',
        'ket_pembayaran',
        'layanan_id',
        'status',
        'unit_dokter',
        'kota',
        'rumah_sakit',
        'spesialisasi',
        'bank',
        'eslon_id',
        'tindakan',
    ];
}
