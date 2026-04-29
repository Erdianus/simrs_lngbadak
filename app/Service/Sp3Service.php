<?php

namespace App\Service;

use App\Http\Controllers\Sp3Controller;
use App\Models\Billing;
use App\Models\Simrs\KipKirimanSimrs;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Simrs\TindakanSimrs;
use App\Models\Simrs\TransaksiAlkesSimrs;
use App\Models\Simrs\TransaksiEmbalaceSimrs;
use App\Models\Simrs\TransaksiKamarSimrs;
use App\Models\Simrs\TransaksiResepSimrs;
use App\Models\Sp3;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Termwind\Components\Li;

class Sp3Service
{

    public static function createSp3($data, $getDataReg, $eselon)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::create([
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
            ]);
            // dd($getDataReg);
            BillingService::createBilling($getDataReg, $sp3, $eselon);
            // dd('test');
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public static function updateSp3($data, $getDataReg, $eselon, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'is_approved_by_verifikator' => false,
            ]);
            Log::info('Existing billings deleted for SP3 ID: ' . $sp3->billings); // ← tambahkan
            if ($sp3->billings()->count() > 0) {
                BillingService::deleteBilling($sp3->id);
            }
            BillingService::createBilling($getDataReg, $sp3, $eselon);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public static function refreshBillSp3($sp3, $getDataReg)
    {
        DB::beginTransaction();
        try {
            if ($sp3->billings()->count() > 0) {
                BillingService::deleteBilling($sp3->id);
            }
            BillingService::createBilling($getDataReg, $sp3, $sp3->eselon);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public static function deleteSp3($slug)
    {
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $value = Sp3::where('slug', $slug)->first();
            $value->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function approveSp3($slug, $no_sp3)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->firstOrFail();
            $no_surat_sp3 = $no_sp3 . '-V/RSBDK1100/' . now()->year . '-S2';
            $sp3->update([
                'no_sp3' => $no_sp3,
                'no_surat_sp3' => $no_surat_sp3,
                'is_approved_by_verifikator' => true
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
