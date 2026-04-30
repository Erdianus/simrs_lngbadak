<?php

namespace App\Service;

use App\Models\Billing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    public static function createBilling($getDataReg, $sp3, $eselon)
    {
        // dd($getDataReg);
        DB::beginTransaction();
        try {
            $billingData = $getDataReg->map(fn($value) => [
                'sp3_id'         => $sp3->id,
                'no_registrasi'  => $value->reg_no,
                'nama_pasien'  => $value->nama,
                'eslon_id'       => $eselon->id,
                'tanggal_masuk'  => $value->tanggal_registrasi,
                'tanggal_keluar' => $value->tanggal_registrasi,
                'keterangan' => $value->keterangan_batal,
            ])->toArray();
            // dd($billingData);
            log::info('Billing data will insert: ' . count($billingData) . ' records'); // ← tambahkan log
            Billing::insert($billingData);
            $billings = Billing::where('sp3_id', $sp3->id)->get();
            log::info('Billing data inserted: ' . count($billings) . ' records'); // ← tambahkan log
            $totalTagihan = $billings->sum(fn($b) => $b->total_biaya_eselon);
            $sp3->update([
                'total_tagihan' => $totalTagihan
            ]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error createBilling: ' . $th->getMessage()); // ← tambahkan log
            return $th->getMessage();
        }
    }

    public static function deleteBilling($sp3Id)
    {
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            Billing::where('sp3_id', $sp3Id)->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function approveBill($slug)
    {
        DB::beginTransaction();
        try {

            // $userId = auth()->id();
            // $role   = auth()->user()->role_name;

            $bill = Billing::where('no_registrasi', $slug)->first();
            $bill->update([
                'approve_verif_pic_by' => auth()->user()->id,
                'is_verified_by_verifikator' => true
            ]);


            // // Validasi alur approval (biar tidak loncat)
            // if ($role == 'PIC Verifikator' && !$billing->approved_pic) {
            //     $billing->approved_verif_pic_by = $userId;
            // } elseif ($role == 'Pengawas Verifikator' && $billing->approved_pic && !$billing->approved_pengawas) {
            //     $billing->approved_verif_pws_by = $userId;
            // } elseif ($role == 'Wakil Direktur' && $billing->approved_pengawas && !$billing->approved_manager) {
            //     $billing->approved_verif_wadir_by = $userId;
            // } elseif ($role == 'Keuangan Admin' && $billing->approved_manager && !$billing->approved_wadir) {
            //     $billing->approved_keu_admin_by = $userId;
            // } else {
            //     return true;
            // }
            // $billing->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
