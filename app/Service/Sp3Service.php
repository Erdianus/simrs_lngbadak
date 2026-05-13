<?php

namespace App\Service;

use App\Models\Billing;
use App\Models\Sp3;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Sp3Service
{

    public static function createSp3Billing($data, $getDataReg, $eselon)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::create([
                'jenis_sp3' => $data['jenis_sp3'],
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
                'keterangan' => $data['keterangan'],
            ]);
            // dd($sp3->computeTotalTagihan());
            $newBill = $getDataReg->pluck('no_registrasi')->toArray();
            $existingBill = Billing::where('eslon_id', $sp3->eslon_id)
                ->where('tanggal_keluar', '>=', $sp3->tgl_masuk)
                ->where('tanggal_keluar', '<=', $sp3->tgl_keluar)
                ->whereIn('no_registrasi', $newBill)
                ->get();
            if ($existingBill->count() > 0) {
                Toastr::error('Data Billing dengan eselon dan tanggal yang sama sudah ada.', 'Error');
                return redirect()->back();
            }
            BillingService::createBilling($getDataReg, $sp3, $eselon);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public static function createSp3($data)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::create([
                'jenis_sp3' => $data['jenis_sp3'],
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
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('New SP3 created: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success',
                'data' => $sp3
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function updateSp3($data, $getDataReg, $eselon, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
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
                'keterangan' => $data['keterangan'],
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

    public static function updateSp3TagihanKeluar($data, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
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
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('SP3 updated: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function updateSp3Deposito($data, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
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
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('SP3 updated: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Berhasil Mengupdate Sp3'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function updateSp3Mcu($data, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
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
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('SP3 updated: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
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

    public static function approveSp3($sp3, $no_sp3)
    {
        DB::beginTransaction();
        try {
            $no_surat_sp3 = str_pad($no_sp3, 4, '0', STR_PAD_LEFT) . '-V/RSBDK1100/' . now()->year . '-S2';
            if ($sp3->no_surat_sp3) {
                $sp3->update([
                    'is_approved_by_verifikator' => true
                ]);
            } else {
                $sp3->update([
                    'no_sp3' => $no_sp3,
                    'no_surat_sp3' => $no_surat_sp3,
                    'is_approved_by_verifikator' => true
                ]);
            }
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Billing berhasil disetujui'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function unapproveSp3($sp3)
    {
        DB::beginTransaction();
        try {
            $sp3->update([
                'is_approved_by_verifikator' => false
            ]);
            Billing::where('sp3_id', $sp3->id)->update([
                'approved_verif_pic_by' => null,
                'is_verified_by_verifikator' => false
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Persetujuan Billing Berhasil dibatalkan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];;
        }
    }
}
