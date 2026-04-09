<?php
namespace App\Service;

use App\Models\Layanan;
use Illuminate\Support\Facades\DB;

class LayananService
{
    public static function createLayanan($data){
        // Logika untuk menyimpan data Layanan ke database
        DB::beginTransaction();
        try {
            Layanan::create([
                'nama'=>$data['nama'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function updateLayanan($slug,$data){
        // Logika untuk menyimpan data Layanan ke database
        DB::beginTransaction();
        try {
            $layanan = Layanan::where('slug', $slug)->first();
            $layanan->update([
                'nama'=>$data['nama'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function deleteLayanan($slug){
        // Logika untuk menghapus data layanan ke database
        DB::beginTransaction();
        try {
            $layanan = Layanan::where('slug', $slug)->first();
            $layanan->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }
}