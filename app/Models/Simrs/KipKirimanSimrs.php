<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KipKirimanSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';
    protected $table = 'kip_kiriman';

    protected $appends = [
        'nama_tindakan',
        'jumlah',
        'total_biaya'
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
        $total = $this->jumlah_kiriman * $this->harga;
        $discount = $total * ($this->discount / 100);
        $total_biaya = $total - $discount;
        return $total_biaya;
    }

    public function getJumlahAttribute()
    {
        return number_format($this->jumlah_kiriman, 0, ',', '.');;
    }

    public function farmalkes()
    {
        return $this->belongsTo(FarmalkesSimrs::class, 'farmalkes_id', 'farmalkes_id');
    }
}
