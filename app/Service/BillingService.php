<?php

namespace App\Service;

use App\Models\Billing;
use App\Models\Sp3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillingService
{
    public static function createBilling($getDataReg, $sp3, $eselon)
    {
        // dd($getDataReg);
        DB::beginTransaction();
        try {
            $billingData = $getDataReg->map(function ($value) use ($sp3, $eselon) {
                do {
                    $slug = Str::random(16);
                } while (Billing::where('slug', $slug)->exists());

                $existingSlugs[] = $slug;

                $tempBilling = new Billing(['no_registrasi' => $value->reg_no]);
                $tempBilling->eslon_id = $eselon->id;

                // Hitung sekali, simpan hasilnya
                $totalBiayaEselon = (int)ceil($tempBilling->countTotalBiayaEselon());
                $deposit          = (int)ceil($tempBilling->countDeposit());

                return [
                    'sp3_id'         => $sp3->id,
                    'no_registrasi'  => $value->reg_no,
                    'no_rm'          => $value->no_mr,
                    'nama_pasien'    => $value->nama,
                    'eslon_id'       => $eselon->id,
                    'tanggal_masuk'  => $value->tanggal_registrasi,
                    'tanggal_keluar' => $value->tanggal_registrasi,
                    'keterangan'     => $value->keterangan_batal,
                    'slug'           => $slug,
                    'biaya_eselon'      => $totalBiayaEselon, // ← disimpan ke DB
                    'biaya_deposit'   => $deposit,
                ];
            })->toArray();
            Billing::insert($billingData);
            $billings = Billing::where('sp3_id', $sp3->id)
                ->selectRaw('
                    SUM(biaya_deposit) as total_deposit,
                    SUM(biaya_eselon) as total_eselon    
                ')
                ->first();
            $totalTagihan = $sp3->jenis_sp3 === 'deposito' ? $billings->total_eselon : $billings->total_eselon - $billings->total_deposit;
            $sp3->update([
                'total_tagihan' => (int)$totalTagihan,
                'total_kunjungan' => $sp3->total_kunjungan,
                'total_pasien' => $sp3->total_pasien
            ]);
            log::info('Billing data inserted: ' . count($billingData) . ' records'); // ← tambahkan log
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error createBilling: ' . $th->getMessage()); // ← tambahkan log
            return $th->getMessage();
        }
    }

    public static function updateBilling($slug)
    {
        DB::beginTransaction();
        try {
            $billing = Billing::where('slug', $slug)->first();
            $billing->update([
                'total_biaya_eselon' => $billing->countTotalBiayaEselon(),
                'deposit' => $billing->countDeposit()
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'berhasil mengupdate Billing'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
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
            $bill = Billing::where('slug', $slug)->first();
            $bill->delete();
            $sp3 = Sp3::findOrFail($bill->sp3_id);
            $billings = $sp3->billings;
            log::info('Billing data inserted: ' . count($billings) . ' records'); // ← tambahkan log
            $totalCob = $billings->sum(fn($b) => $b->cob);
            $totalDeposit = $billings->sum(fn($b) => $b->deposit);
            $totalBiayaEselon = $billings->sum(fn($b) => $b->total_biaya_eselon);
            $totalTagihan = $sp3->jenis_sp3 === 'deposito' ? $billings->sum(fn($b) => $b->total_biaya_eselon) : ($totalBiayaEselon - $totalDeposit) - $totalCob;
            $sp3->update([
                'total_tagihan' => $totalTagihan,
            ]);
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

    public static function approveAllBills($billings)
    {
        DB::beginTransaction();
        try {
            $ids = $billings->pluck('id');
            Billing::whereIn('id', $ids)->update([
                'approved_verif_pic_by' => auth()->user()->id,
                'is_verified_by_verifikator' => true
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Billing Berhasil Diverifikasi'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
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
                'approved_verif_pic_by' => auth()->user()->id,
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
                'approved_verif_pic_by' => null,
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
            Billing::create($billingData);
            $billings = $sp3->billings;
            $totalCob = $billings->sum(fn($b) => $b->cob);
            $totalBiayaEselon = $billings->sum(fn($b) => $b->total_biaya_eselon);
            $totalTagihan = $totalBiayaEselon - $totalCob;
            $sp3->update([
                'total_tagihan' => $totalTagihan,
            ]);
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

    public static function createBillMcu($sp3, $mcu)
    {
        DB::beginTransaction();
        try {
            $billingData = [
                'sp3_id'         => $sp3->id,
                'no_registrasi'  => $mcu->reg_no,
                'no_rm'  => $mcu->no_mr,
                'nama_pasien'  => $mcu->nama,
                'eslon_id'       => $sp3->eslon_id,
                'tanggal_masuk'  => $mcu->tanggal_registrasi,
                'tanggal_keluar' => $mcu->tanggal_registrasi,
                'keterangan' => $mcu->keterangan,
                'biaya' => (int) ceil($mcu->total_biaya_eselon)
            ];
            // dd($billingData);
            Billing::create($billingData);
            $billings = Billing::where('sp3_id', $sp3->id)->get();
            $totalCob = $billings->sum(fn($b) => $b->cob);
            $totalBiayaEselon = $billings->sum(fn($b) => $b->biaya);
            $totalDeposit = $billings->sum(fn($b) => $b->deposit);
            $totalTagihan = ($totalBiayaEselon - $totalDeposit) - $totalCob;
            $sp3->update([
                'total_tagihan' => $totalTagihan,
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Billing MCU berhasil ditambahkan'
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

    public static function addCob($value)
    {
        DB::beginTransaction();
        try {
            // dd($value);
            $bill = Billing::with('sp3')->where('slug', $value['slug'])->first();
            $bill->update([
                'cob' => $value['total_cob']
            ]);
            $sp3 = $bill->sp3;
            $billings = Billing::where('sp3_id', $sp3->id)->get();
            $totalDeposit = $billings->sum(fn($b) => $b->deposit);
            $totalCob = $billings->sum(fn($b) => $b->cob);
            $totalBiayaEselon = $billings->sum(fn($b) => $sp3->jenis_sp3 === 'deposito' ? $b->biaya : $b->total_biaya_eselon);
            log::info('Billing data inserted: ' . count($billings) . ' records'); // ← tambahkan log
            $totalTagihan = ($sp3->jenis_sp3 === 'deposito' ? $totalBiayaEselon : $totalBiayaEselon - $totalDeposit) - $totalCob;
            $sp3->update([
                'total_tagihan' => $totalTagihan
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Berhasil Menambahkan COB'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }
}
