<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiAlkesSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';
    protected $table = 'transaksi_alkes';

    protected $appends = [
        'nama_tindakan',
        'total_biaya',
        'jenis_tindakan',
        'jumlah'
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
        $total = $this->jumlah_jual * $this->harga_jual;
        $discount = $total * ($this->discount / 100);
        $total_biaya = $total - $discount;
        return $total_biaya;
    }

    public function getJumlahAttribute()
    {
        return number_format($this->jumlah_jual, 0, ',', '.');
    }

    public function getJenisTindakanAttribute()
    {
        return 'BMHP';
    }

    public function farmalkes()
    {
        return $this->belongsTo(FarmalkesSimrs::class, 'alkes_id', 'farmalkes_id');
    }
}
