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
                'no_rm'  => $value->no_mr,
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
            $totalDeposit = $billings->sum(fn($b) => $b->deposit);
            log::info('Billing data inserted: ' . count($billings) . ' records'); // ← tambahkan log
            $totalTagihan = $sp3->jenis_sp3 === 'deposito' ? $billings->sum(fn($b) => $b->total_biaya_eselon) : $billings->sum(fn($b) => $b->total_biaya_eselon) - $totalDeposit;
            $sp3->update([
                'total_tagihan' => $totalTagihan,
                'total_kunjungan' => $sp3->total_kunjungan,
                'total_pasien' => $sp3->total_pasien
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

    public static function deleteSingleBilling($slug)
    {
        DB::beginTransaction();
        try {
            Billing::where('slug', $slug)->first()->delete();
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Billing Berhasil Dihapus'
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function approveBill($id)
    {
        DB::beginTransaction();
        try {
            $bill = Billing::find($id);
            $bill->update([
                'approve_verif_pic_by' => auth()->user()->id,
                'is_verified_by_verifikator' => true
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public static function unapproveBill($id)
    {
        DB::beginTransaction();
        try {
            $bill = Billing::find($id);
            $bill->update([
                'approve_verif_pic_by' => null,
                'is_verified_by_verifikator' => false
            ]);
            // dd($bill);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public static function createBillDeposito($sp3, $deposit)
    {
        DB::beginTransaction();
        try {
            $billingData = [
                'sp3_id'         => $sp3->id,
                'no_registrasi'  => $deposit->no_reg,
                'no_rm'  => $deposit->registrasi->no_mr,
                'nama_pasien'  => $deposit->registrasi->nama,
                'eslon_id'       => $sp3->eslon_id,
                'tanggal_masuk'  => $deposit->update_date,
                'tanggal_keluar' => $deposit->update_date,
                'keterangan' => $deposit->keterangan,
                'biaya' => (int) ceil($deposit->jumlah_deposit)
            ];
            // dd($billingData);
            $createBill = Billing::create($billingData);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Deposit berhasil ditambahkan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }
}
