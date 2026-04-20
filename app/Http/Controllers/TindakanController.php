<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Simrs\KipKirimanSimrs;
use App\Models\Simrs\TindakanSimrs;
use App\Models\Simrs\TransaksiAlkesSimrs;
use App\Models\Simrs\TransaksiEmbalaceSimrs;
use App\Models\Simrs\TransaksiKamarSimrs;
use App\Models\Simrs\TransaksiResepSimrs;
use Illuminate\Http\Request;

class TindakanController extends Controller
{
    public function listTindakanBill($bill)
    {
        $billing = Billing::where('slug', $bill)->first();
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'jumlah_jual', 'alkes_id'])
            ->where('reg_no', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $resepRawatJalan = TransaksiResepSimrs::select(['regnum', 'jumlah_dijual', 'harga_jual', 'discount', 'payment', 'farmalkes_id'])
            ->where('regnum', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $resepRawatInap = KipKirimanSimrs::select(['no_reg', 'farmalkes_id', 'jumlah_kiriman', 'kiriman_id', 'payment', 'harga', 'discount'])
            ->where('no_reg', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $kamar = TransaksiKamarSimrs::select(['no_reg', 'id_kamar', 'lama_hari', 'keterangan', 'tarif_sewa', 'discount'])
            ->where('no_reg', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $embalace = TransaksiEmbalaceSimrs::select(['no_reg', 'ppn', 'discount'])
            ->where('no_reg', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        return view('tindakan.detail-list-tindakan', compact('billing', 'tindakan', 'alkes', 'resepRawatInap', 'resepRawatJalan', 'kamar', 'embalace'));
    }
}
