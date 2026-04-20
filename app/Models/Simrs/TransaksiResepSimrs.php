<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiResepSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';
    protected $table = 'transaksi_resep';

    protected $appends = [
        'nama_tindakan',
        'jumlah',
        'total_biaya',
        'jenis_tindakan',
    ];

    public function getNamaTindakanAttribute()
    {
        $tindakan = is_null($this->payment)
            ? optional($this->farmalkes)->farmalkes_desc
            : '-';
        return $tindakan;
    }

    public function getTotalBiayaAttribute()
    {
        $total = $this->jumlah_dijual * $this->harga_jual;
        $discount = $total * ($this->discount / 100);
        $total_biaya = $total - $discount;
        return $total_biaya;
    }

    public function getJumlahAttribute()
    {
        return number_format($this->jumlah_dijual, 0, ',', '.');;
    }

    public function getJenisTindakanAttribute()
    {
        return 'Resep';
    }

    public function farmalkes()
    {
        return $this->belongsTo(FarmalkesSimrs::class, 'farmalkes_id', 'farmalkes_id');
    }
}
