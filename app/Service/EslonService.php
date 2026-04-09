<?php
namespace App\Service;

use App\Models\Eslon;
use Illuminate\Support\Facades\DB;

class EslonService
{
    public static function createEslon($data){
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        try {
            Eslon::create([
                'deskripsi'=>$data['deskripsi'],
                'nama'=>$data['nama'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function updateEslon($slug,$data){
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        $eslon = Eslon::where('slug',$slug)->first();
        try {
            $eslon->update([
                'nama'=>$data['nama'],
                'deskripsi'=>$data['deskripsi'],
                ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function deleteEslon($slug){
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $eslon = Eslon::where('slug',$slug)->first();
            $eslon->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }
}