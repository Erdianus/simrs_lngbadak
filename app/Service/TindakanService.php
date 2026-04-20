<?php

namespace App\Service;

use App\Models\Tindakan;
use Illuminate\Support\Facades\DB;

class TindakanService
{
    public static function createTindakan($billing, $data)
    {

        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        try {
            foreach ($data as $group => $items) {
                // Skip jika tidak ada mapping atau data kosong
                if (!isset($data[$group]) || empty($items)) continue;

                $map = $data[$group];
                if ($group == 'tindakan') {
                    foreach ($items as $item) {
                        Tindakan::create([
                            'billing_id'       => $billing->id,
                            'nama_tindakan'    => $item[$map['nama_tindakan']],
                            'jumlah'           => $item[$map['jumlah']],
                            'discount'         => $item['discount'],
                            'jenis_transaksi'  => $group,
                            'biaya'            => $item[$map['biaya']],
                        ]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function updateTindakan($id, $data)
    {
        // Logika untuk menyimpan data Eslon ke database
        DB::beginTransaction();
        $value = Tindakan::find($id)->first();
        try {
            $value->update([
                'billing_id' => $data['billing_id'],
                'nama_tindakan' => $data['nama_tindakan'],
                'jumlah' => $data['jumlah'],
                'discount' => $data['discount'],
                'jenis_transaksi' => $data['jenis_transaksi'],
                'biaya' => $data['biaya'],
            ]);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function deleteTindakan($id)
    {
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $value = Tindakan::findOrFail($id)->first();
            $value->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }
}
