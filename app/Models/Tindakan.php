<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tindakan extends Model
{
    use HasFactory;

    protected $table = 'tindakans';

    protected $fillable = [
        'billing_id',
        'nama_tindakan',
        'jumlah',
        'discount',
        'jenis_transaksi',
        'biaya',
        'payment',
    ];

    protected $appends = [
        'total_biaya'
    ];

    public function getTotalBiayaAttribute()
    {
        $total = $this->jumlah * $this->biaya;
        $discount = $total * ($this->discount / 100);
        $total_biaya = $total - $discount;
        return $total_biaya;
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }
}
