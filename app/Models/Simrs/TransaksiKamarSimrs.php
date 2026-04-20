<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKamarSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';
    protected $table = 'trans_kamar';

    protected $appends = [
        'total_biaya',
        'jumlah'
    ];

    public function getTotalBiayaAttribute()
    {
        $total = $this->lama_hari * $this->tarif_sewa;
        $discount = $total * ($this->discount / 100);
        $total_biaya = $total - $discount;
        return $total_biaya;
    }

    public function getJumlahAttribute()
    {
        return $this->lama_hari;
    }
}
