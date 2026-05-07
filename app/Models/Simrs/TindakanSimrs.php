<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakanSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';
    protected $table = 'transaksi_tindakan';

    protected $appends = [
        'nama_tindakan',
        'total_biaya',
        'jenis_tindakan'
    ];

    public function getNamaTindakanAttribute()
    {
        $tindakan = $this->masterTindakanPerusahaan
            ? optional($this->masterTindakanPerusahaan)->tindakan_desc
            : optional($this->masterTindakan)->tindakan_desc;
        return $tindakan;
    }

    public function getTotalBiayaAttribute()
    {
        $total = $this->jumlah * $this->tindakan_biaya;
        $discount = $total * ($this->discount / 100);
        $total_biaya = $total - $discount;
        return $total_biaya;
    }

    public function getJenisTindakanAttribute()
    {
        return 'Tindakan';
    }

    public function masterTindakan()
    {
        return $this->belongsTo(MasterTindakanSimrs::class, 'tindakan_id', 'tindakan_id');
    }
    public function masterTindakanPerusahaan()
    {
        return $this->belongsTo(MasterTindakanPerusahaanSimrs::class, 'tindakan_id', 'tindakan_id');
    }
}
