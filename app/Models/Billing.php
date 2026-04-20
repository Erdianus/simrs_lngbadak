<?php

namespace App\Models;

use App\Models\Simrs\KipKirimanSimrs;
use App\Models\Simrs\TindakanSimrs;
use App\Models\Simrs\TransaksiAlkesSimrs;
use App\Models\Simrs\TransaksiEmbalaceSimrs;
use App\Models\Simrs\TransaksiKamarSimrs;
use App\Models\Simrs\TransaksiResepSimrs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
        'approved_verif_pic_by',
        'approved_verif_pws_by',
        'approved_verif_wadir_by',
        'approved_keu_admin_by',
        'slug'
    ];

    protected $appends = [
        'total_tindakan',
        'total_BMHP',
        'total_resep',
        'total_KIP',
        'total_PPN',
        'total_biaya_eselon',
        'total_biaya_kas',
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

    // public function getBiayaFormatedAttribute()
    // {
    //     return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    // }

    public function getTotalTindakanAttribute()
    {
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'tindakan_biaya'])
            ->where('reg_no', $this->no_registrasi)
            ->get();
        return $tindakan->sum('total_biaya');
    }

    public function getTotalBMHPAttribute()
    {
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'jumlah_jual', 'harga_jual'])
            ->where('reg_no', $this->no_registrasi)
            ->get();
        return $alkes->sum('total_biaya');
    }

    public function getTotalResepAttribute()
    {
        $resepRawatJalan = TransaksiResepSimrs::select(['regnum', 'jumlah_dijual', 'harga_jual', 'discount'])
            ->where('regnum', $this->no_registrasi)
            ->get();
        return $resepRawatJalan->sum('total_biaya');
    }

    public function getTotalKIPAttribute()
    {
        $resepRawatInap = KipKirimanSimrs::select(['no_reg', 'jumlah_kiriman', 'harga', 'discount'])
            ->where('no_reg', $this->no_registrasi)
            ->get();
        return $resepRawatInap->sum('total_biaya');
    }

    public function getTotalPPNAttribute()
    {
        $embalace = TransaksiEmbalaceSimrs::select(['no_reg', 'ppn', 'discount'])
            ->where('no_reg', $this->no_registrasi)
            ->get();
        return $embalace->sum('ppn');
    }

    public function getTotalBiayaEselonAttribute()
    {
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'jumlah_jual', 'alkes_id'])
            ->where('reg_no', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $resepRawatJalan = TransaksiResepSimrs::select(['regnum', 'jumlah_dijual', 'harga_jual', 'discount', 'payment', 'farmalkes_id'])
            ->where('regnum', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $resepRawatInap = KipKirimanSimrs::select(['no_reg', 'farmalkes_id', 'jumlah_kiriman', 'kiriman_id', 'payment', 'harga', 'discount'])
            ->where('no_reg', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $kamar = TransaksiKamarSimrs::select(['no_reg', 'id_kamar', 'lama_hari', 'keterangan', 'tarif_sewa', 'discount'])
            ->where('no_reg', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $embalace = TransaksiEmbalaceSimrs::select(['no_reg', 'ppn', 'discount'])
            ->where('no_reg', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();

        // ====== DEBUG LOG ======
        Log::info("=== Billing: {$this->no_registrasi} ===", [
            'tindakan_count'       => $tindakan->count(),
            'tindakan_total'       => $tindakan->sum('total_biaya'),
            'alkes_count'          => $alkes->count(),
            'alkes_total'          => $alkes->sum('total_biaya'),
            'resepRJ_count'        => $resepRawatJalan->count(),
            'resepRJ_total'        => $resepRawatJalan->sum('total_biaya'),
            'resepRI_count'        => $resepRawatInap->count(),
            'resepRI_total'        => $resepRawatInap->sum('total_biaya'),
            'kamar_count'          => $kamar->count(),
            'kamar_total'          => $kamar->sum('total_biaya'),
            'embalace_count'       => $embalace->count(),
            'embalace_ppn_total'   => $embalace->sum('ppn'),
        ]);

        $totalEselon = ($tindakan->sum('total_biaya')) + ($alkes->sum('total_biaya')) + ($resepRawatJalan->sum('total_biaya')) + ($resepRawatInap->sum('total_biaya')) + ($kamar->sum('total_biaya')) + ($embalace->sum('ppn'));
        return $totalEselon;
    }

    public function getTotalBiayaKasAttribute()
    {
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $this->no_registrasi)
            ->where('payment', 'C')
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'jumlah_jual', 'alkes_id'])
            ->where('reg_no', $this->no_registrasi)
            ->where('payment', 'C')
            ->get();
        $resepRawatJalan = TransaksiResepSimrs::select(['regnum', 'jumlah_dijual', 'harga_jual', 'discount', 'payment', 'farmalkes_id'])
            ->where('regnum', $this->no_registrasi)
            ->where('payment', 'C')
            ->get();
        $resepRawatInap = KipKirimanSimrs::select(['no_reg', 'farmalkes_id', 'jumlah_kiriman', 'kiriman_id', 'harga', 'discount'])
            ->where('no_reg', $this->reg_no)
            ->where('payment', 'C')
            ->get();
        $kamar = TransaksiKamarSimrs::select(['no_reg', 'id_kamar', 'lama_hari', 'keterangan', 'tarif_sewa', 'discount'])
            ->where('no_reg', $this->reg_no)
            ->where('payment', 'C')
            ->get();
        $embalace = TransaksiEmbalaceSimrs::select(['no_reg', 'ppn', 'discount'])
            ->where('no_reg', $this->reg_no)
            ->where('payment', 'C')
            ->get();

        $totalKas = ($tindakan->sum('total_biaya')) + ($alkes->sum('total_biaya')) + ($resepRawatJalan->sum('total_biaya')) + ($resepRawatInap->sum('total_biaya')) + ($kamar->sum('total_biaya')) + ($embalace->sum('ppn'));
        return $totalKas;
    }

    public function sp3()
    {
        return $this->belongsTo(Sp3::class, 'sp3_id', 'id');
    }

    public function tindakan()
    {
        return $this->hasMany(Tindakan::class, 'billing_id', 'id');
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
