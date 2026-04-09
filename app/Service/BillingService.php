<?php
namespace App\Service;

use App\Models\Billing;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public static function createBilling($data){
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        try {
            Billing::create([
                'sp3_id'=>$data['sp3_id'],
                'keterangan'=>$data['keterangan'],
                'no_registrasi'=>$data['no_registrasi'],
                'eslon_id'=>$data['eslon_id'],
                'layanan_id'=>$data['layanan_id'],
                'sub_layanan_id'=>$data['sub_layanan_id'],
                'tanggal_masuk'=>$data['tanggal_masuk'],
                'tanggal_keluar'=>$data['tanggal_keluar'],
                'biaya'=>$data['biaya'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function updateBilling($slug,$data){
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        $value = Billing::where('slug',$slug)->first();
        try {
            $value->update([
                'sp3_id'=>$data['sp3_id'],
                'keterangan'=>$data['keterangan'],
                'no_registrasi'=>$data['no_registrasi'],
                'eslon_id'=>$data['eslon_id'],
                'layanan_id'=>$data['layanan_id'],
                'sub_layanan_id'=>$data['sub_layanan_id'],
                'tanggal_masuk'=>$data['tanggal_masuk'],
                'tanggal_keluar'=>$data['tanggal_keluar'],
                'biaya'=>$data['biaya'],
                ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function deleteBilling($slug){
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $value = Billing::where('slug',$slug)->first();
            $value->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }
}