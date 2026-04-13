<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billings';

    protected $fillable = [
        'sp3_id',
        'keterangan',
        'no_registrasi',
        'eslon_id',
        'layanan_id',
        'sub_layanan_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'biaya',
        'is_verified_by_verifikator',
        'is_verified_by_keuangan',
        'slug'
    ];

    protected $appends = [
        'biaya_formated'
    ];

    protected static function booted()
    {
        static::creating(function (Billing $billing) {
            $billing->slug = $billing->slug ?: static::generateUniqueSlug($billing->no_registrasi);
        });

        static::updating(function (Billing $billing) {
            if ($billing->isDirty('no_registrasi') || empty($billing->slug)) {
                $billing->slug = static::generateUniqueSlug($billing->no_registrasi, $billing->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $value, int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getBiayaFormatedAttribute()
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    public function sp3()
    {
        return $this->belongsTo(Sp3::class, 'sp3_id', 'id');
    }

    public function eselon()
    {
        return $this->belongsTo(Eslon::class, 'eslon_id', 'id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id');
    }

    public function sub_layanan()
    {
        return $this->belongsTo(SubLayanan::class, 'sub_layanan_id', 'id');
    }
}
