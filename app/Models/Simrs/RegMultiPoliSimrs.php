<?php

namespace App\Models\Simrs;

use App\Models\Eslon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegMultiPoliSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';

    protected $table = 'reg_multi_poli';

    protected $appends = [
        'eselon_id',
        'total_biaya_eselon',
        'deposit',
    ];

    public function getEselonIdAttribute()
    {
        $eselon = Eslon::select('id')->where('nama', $this->eselon)->first();
        return $eselon->id;
    }

    public function getTotalBiayaEselonAttribute()
    {
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $this->reg_no)
            ->where('payment', NULL)
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'harga_jual', 'jumlah_jual', 'alkes_id'])
            ->where('reg_no', $this->reg_no)
            ->where('payment', NULL)
            ->get();
        $resepRawatJalan = TransaksiResepSimrs::select(['regnum', 'jumlah_dijual', 'harga_jual', 'discount', 'payment', 'farmalkes_id'])
            ->where('regnum', $this->reg_no)
            ->where('payment', NULL)
            ->get();
        $resepRawatInap = KipKirimanSimrs::select(['no_reg', 'farmalkes_id', 'jumlah_kiriman', 'kiriman_id', 'payment', 'harga', 'discount'])
            ->where('no_reg', $this->reg_no)
            ->where('payment', NULL)
            ->get();
        $kamar = TransaksiKamarSimrs::select(['no_reg', 'id_kamar', 'lama_hari', 'keterangan', 'tarif_sewa', 'discount'])
            ->where('no_reg', $this->reg_no)
            ->where('payment', NULL)
            ->get();
        $embalace = TransaksiEmbalaceSimrs::select(['no_reg', 'ppn', 'discount'])
            ->where('no_reg', $this->reg_no)
            ->where('payment', NULL)
            ->get();

        $totalEselon = ($tindakan->sum('total_biaya')) + ($alkes->sum('total_biaya')) + ($resepRawatJalan->sum('total_biaya')) + ($resepRawatInap->sum('total_biaya')) + ($kamar->sum('total_biaya')) + ($embalace->sum('ppn'));
        if ($resepRawatInap->count() > 0 || $kamar->count() > 0) {
            $ref_adm = ReferensiAdmSimrs::select(['besar_fee', 'max_besar'])->where('kode_eselon', $this->eselon->nama)->first();
            $biayaAdm = $kamar->sum('tarif_sewa') == 0 ? 0 : ceil($totalEselon * ($ref_adm->besar_fee / 100));
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
        $deposit = DepositKamarSimrs::select('jumlah_deposit')->where('no_reg', $this->reg_no)->first();
        if ($deposit) {
            return $deposit->jumlah_deposit;
        }
        return 0;
    }

    public function masterPoli()
    {
        return $this->belongsTo(MasterPoliSimrs::class, 'kode_poli', 'poli_id');
    }

    public function eselon()
    {
        return $this->belongsTo(EselonSimrs::class, 'eselon', 'kode_eselon');
    }

    public function transaksiKamar()
    {
        return $this->hasMany(TransaksiKamarSimrs::class, 'no_reg', 'reg_no');
    }
}
