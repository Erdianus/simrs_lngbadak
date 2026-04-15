<?php

namespace App\Service;

use App\Models\Billing;
use App\Models\Sp3;
use Illuminate\Support\Facades\DB;

class Sp3Service
{
    public static function createSp3($data)
    {
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        try {
            Sp3::create([
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'jumlah_pasien' => $data['jumlah_pasien'],
                'jumlah_kunjungan' => $data['jumlah_kunjungan'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
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
