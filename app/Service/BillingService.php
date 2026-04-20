<?php

namespace App\Service;

use App\Models\Billing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    public static function createBilling($value, $sp3, $eselon)
    {
        DB::beginTransaction();
        try {
            $data = [
                'sp3_id'        => $sp3->id,
                'no_registrasi' => $value->reg_no,
                'eslon_id'      => $eselon->id,
                'tanggal_masuk' => $value->tanggal_registrasi,
                'tanggal_keluar' => $value->tanggal_registrasi
            ];

            // DEBUG: Cek data sebelum insert
            Log::info('Data billing akan disimpan:', $data);
            // dd($data); // ← aktifkan sementara untuk cek

            $billing = Billing::create($data);

            // DEBUG: Cek apakah billing berhasil dibuat
            Log::info('Billing created:', $billing->toArray());

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error createBilling: ' . $th->getMessage()); // ← tambahkan log
            return $th->getMessage();
        }
    }

    public static function updateBilling($slug, $data)
    {
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        $value = Billing::where('slug', $slug)->first();
        try {
            $value->update([
                'sp3_id' => $data['sp3_id'],
                'keterangan' => $data['keterangan'],
                'no_registrasi' => $data['no_registrasi'],
                'eslon_id' => $data['eslon_id'],
                'layanan_id' => $data['layanan_id'],
                'sub_layanan_id' => $data['sub_layanan_id'],
                'tanggal_masuk' => $data['tanggal_masuk'],
                'tanggal_keluar' => $data['tanggal_keluar'],
                'biaya' => $data['biaya'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function deleteBilling($slug)
    {
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $value = Billing::where('slug', $slug)->first();
            $value->delete();
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
            $billing = Billing::where('slug', $slug)->firstOrFail();

            $userId = auth()->id();
            $role   = auth()->user()->role_name;

            // Validasi alur approval (biar tidak loncat)
            if ($role == 'PIC Verifikator' && !$billing->approved_pic) {
                $billing->approved_verif_pic_by = $userId;
            } elseif ($role == 'Pengawas Verifikator' && $billing->approved_pic && !$billing->approved_pengawas) {
                $billing->approved_verif_pws_by = $userId;
            } elseif ($role == 'Wakil Direktur' && $billing->approved_pengawas && !$billing->approved_manager) {
                $billing->approved_verif_wadir_by = $userId;
            } elseif ($role == 'Keuangan Admin' && $billing->approved_manager && !$billing->approved_wadir) {
                $billing->approved_keu_admin_by = $userId;
            } else {
                return true;
            }
            $billing->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
