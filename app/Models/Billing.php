<?php

namespace App\Models;

use App\Models\Simrs\DepositKamarSimrs;
use App\Models\Simrs\KipKirimanSimrs;
use App\Models\Simrs\ReferensiAdmSimrs;
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
        'nama_pasien',
        'eslon_id',
        'layanan_id',
        'sub_layanan_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'biaya',
        'no_rm',
        'is_verified_by_verifikator',
        'is_verified_by_keuangan',
        'approved_verif_pic_by',
        'approved_verif_pws_by',
        'approved_verif_wadir_by',
        'approved_keu_admin_by',
        'slug',
        'cob',
        'biaya_eselon',
        'biaya_deposit'

    ];

    protected $appends = [
        // 'total_tindakan',
        // 'total_BMHP',
        // 'total_resep',
        // 'total_KIP',
        // 'total_sewa_kamar',
        // 'total_PPN',
        // 'total_biaya_eselon',
        // 'total_biaya_kas',
        // 'deposit'
    ];

    protected static function booted()
    {
        static::creating(function (Billing $billing) {
            $billing->slug = $billing->slug ?: static::generateUniqueSlug();
        });

        static::updating(function (Billing $billing) {
            if (empty($billing->slug)) {
                $billing->slug = static::generateUniqueSlug();
            }
        });
    }

    protected static function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(16);
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTotalBiayaSp3Attribute()
    {
        return $this->total_biaya_eselon - $this->deposit - $this->cob;
    }

    public function countTotalBiayaEselon(): int
    {
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'harga_jual', 'jumlah_jual', 'alkes_id'])
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

        $totalEselon = ($tindakan->sum('total_biaya')) + ($alkes->sum('total_biaya')) + ($resepRawatJalan->sum('total_biaya')) + ($resepRawatInap->sum('total_biaya')) + ($kamar->sum('total_biaya')) + ($embalace->sum('ppn'));
        if ($resepRawatInap->count() > 0 || $kamar->count() > 0) {
            $ref_adm = ReferensiAdmSimrs::select(['besar_fee', 'max_besar'])->where('kode_eselon', $this->eselon->nama)->first();
            $biayaAdm = ceil($totalEselon * ($ref_adm->besar_fee / 100));
            $total = $totalEselon + $biayaAdm;
            if ($biayaAdm > $ref_adm->max_besar) {
                $totalEselon = $totalEselon + ($ref_adm->max_besar);
            } else {
                $totalEselon = $total;
            }
        }
        return $totalEselon;
    }

    public function countDeposit(): int
    {
        $deposit = DepositKamarSimrs::select('jumlah_deposit')->where('no_reg', $this->no_registrasi)->first();
        if ($deposit) {
            return $deposit->jumlah_deposit;
        }
        return 0;
    }

    public function getTotalBiayaEselonAttribute()
    {
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $this->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'harga_jual', 'jumlah_jual', 'alkes_id'])
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

        $totalEselon = ($tindakan->sum('total_biaya')) + ($alkes->sum('total_biaya')) + ($resepRawatJalan->sum('total_biaya')) + ($resepRawatInap->sum('total_biaya')) + ($kamar->sum('total_biaya')) + ($embalace->sum('ppn'));
        if ($resepRawatInap->count() > 0 || $kamar->count() > 0) {
            $ref_adm = ReferensiAdmSimrs::select(['besar_fee', 'max_besar'])->where('kode_eselon', $this->eselon->nama)->first();
            $biayaAdm = ceil($totalEselon * ($ref_adm->besar_fee / 100));
            $total = $totalEselon + $biayaAdm;
            if ($biayaAdm > $ref_adm->max_besar) {
                $totalEselon = $totalEselon + ($ref_adm->max_besar);
            } else {
                $totalEselon = $total;
            }
        }
        return $totalEselon;
    }

    public function getDepositAttribute()
    {
        $deposit = DepositKamarSimrs::select('jumlah_deposit')->where('no_reg', $this->no_registrasi)->first();
        if ($deposit) {
            return $deposit->jumlah_deposit;
        }
        return 0;
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
