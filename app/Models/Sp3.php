<?php

namespace App\Models;

use App\Models\Eslon;
use App\Models\Simrs\EselonSimrs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sp3 extends Model
{
    use HasFactory;

    protected $table = 'sp3s';

    protected $fillable = [
        'no_sp3',
        'jenis_sp3',
        'no_surat_sp3',
        'keterangan',
        'tgl_sp3',
        'jenis_surat',
        'nomor_tagihan',
        'tgl_terima_keu',
        'perihal_tagihan_id',
        'ket_inv_pasien',
        'ket_inv_rs',
        'eslon_id',
        'ket_pembayaran',
        'layanan_id',
        'kota',
        'nama_rs',
        'dokter_rujukan',
        'tgl_masuk',
        'tgl_keluar',
        'total_tagihan',
        'cob',
        'pasien',
        'kunjungan',
        'is_approved_by_verifikator',
        'is_approved_by_keuangan',
        'slug',
    ];

    protected $appends = [
        'total_pasien',
        'total_kunjungan',
        'total_biaya'
    ];

    protected static function booted()
    {
        static::creating(function (Sp3 $sp3) {
            if ($sp3->nomor_tagihan) {
                $sp3->slug = $sp3->slug ?: static::generateUniqueSlug($sp3->slugSource());
            }
        });

        static::updating(function (Sp3 $sp3) {
            if ((($sp3->isDirty('nomor_tagihan') || $sp3->isDirty('eslon_id')) && $sp3->nomor_tagihan) || empty($sp3->slug)) {
                $sp3->slug = static::generateUniqueSlug($sp3->slugSource(), $sp3->id);
            }
        });
    }

    protected function slugSource(): string
    {
        $eslonName = null;

        if ($this->eslon_id) {
            $eslonName = Eslon::find($this->eslon_id)->nama ?? null;
        }

        $eslonPart = $eslonName ?: $this->eslon_id;

        return trim($this->nomor_tagihan . ' ' . $eslonPart);
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

    public function getTotalBiayaAttribute()
    {
        if ($this->jenis_sp3 === "deposito") {
            $total = $this->billings->sum(fn($b) => $b->biaya);
        } else {
            return null;
        }
        return $total;
        // return 'Rp ' . number_format($total, 0, ',', '.');
    }

    public function getTotalKunjunganAttribute()
    {
        $totalKunjungan = $this->billings->count();
        return $totalKunjungan;
    }

    public function getTotalPasienAttribute()
    {
        $totalPasiens = $this->billings->unique('no_rm')->count();
        return $totalPasiens;
    }


    public function billings()
    {
        return $this->hasMany(Billing::class, 'sp3_id', 'id');
    }

    public function eselon()
    {
        return $this->belongsTo(Eslon::class, 'eslon_id', 'id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id');
    }

    public function perihalTagihan()
    {
        return $this->belongsTo(PerihalTagihan::class, 'perihal_tagihan_id', 'id');
    }
}
