<?php
namespace App\Service;

use App\Models\SubLayanan;
use Illuminate\Support\Facades\DB;

class SubLayananService
{
    public static function createSubLayanan($data){
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        try {
            SubLayanan::create([
                'nama'=>$data['nama'],
                'layanan_id'=>$data['layanan_id'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function updateSubLayanan($slug,$data){
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        try {
            $layanan = SubLayanan::where('slug', $slug)->first();
            $layanan->update([
                'nama'=>$data['nama'],
                'layanan_id'=>$data['layanan_id'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function deleteSubLayanan($slug){
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $layanan = SubLayanan::where('slug', $slug)->first();
            $layanan->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }
}