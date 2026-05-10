<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Sp3;
use Illuminate\Http\Request;

class McuController extends Controller
{
    public function getRegMcuData(Request $request)
    {
        $sp3 = Sp3::where('slug', $request->sp3_slug)->first();
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column'];
        $columnName      = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue     = $search_arr['value'];

        // Base query closure untuk reuse
        $baseQuery = fn() => RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
            ->with(['masterPoli', 'eselon'])
            ->where('kode_poli', 'MCU01')
            ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$sp3->tgl_masuk, $sp3->tgl_keluar])
            ->where('eselon', $sp3->eselon->nama)
            ->orderByDesc('tanggal_registrasi');

        // Filter closure untuk reuse
        $filterGroup = fn($group) => !($group->count() === 1 && $group->first()->jadi === 'Y');

        // Map closure untuk reuse
        $mapGroup = function ($group) {
            $dataBatal = $group->where('jadi', 'Y');
            $adaBatal  = $group->count() > 1 && $dataBatal->count() > 0;
            $dataUtama = $group->where('jadi', '!=', 'Y')->first() ?? $group->first();

            if ($adaBatal) {
                $jumlahBatal = $dataBatal->count();
                $namaPoli    = $dataBatal->map(fn($item) => $item->masterPoli?->poli_name ?? $item->kode_poli);

                $dataUtama->keterangan_batal = $jumlahBatal === 1
                    ? "Memiliki registrasi batal pada poli: {$namaPoli->first()}"
                    : "Memiliki {$jumlahBatal} registrasi batal pada poli: " . $namaPoli->map(fn($poli, $i) => ($i + 1) . ". {$poli}")->implode(', ');
            } else {
                $dataUtama->keterangan_batal = null;
            }

            return $dataUtama;
        };

        $totalRecords = $baseQuery()
            ->get()
            ->groupBy('reg_no')
            ->filter($filterGroup)
            ->map($mapGroup)
            ->flatten()
            ->unique('reg_no')
            ->count();

        $totalRecordsWithFilter = $baseQuery()
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('reg_no', 'like', '%' . $searchValue . '%');
                $query->orWhere('nama', 'like', '%' . $searchValue . '%');
            })
            ->get()
            ->groupBy('reg_no')
            ->filter($filterGroup)
            ->flatten()
            ->unique('reg_no')
            ->count();

        $records = $baseQuery()
            ->get()
            ->groupBy('reg_no')
            ->filter($filterGroup)
            ->map($mapGroup)
            ->flatten()
            ->unique('reg_no');
        $data_arr = [];
        foreach ($records as $record) {
            $modify = '
            <td class="text-end"> 
                <div class="actions">
                    <a data-no-reg="' . $record->reg_no . '" 
                    data-url="' . url('billing/mcu/' . $sp3->slug . '/' . $record->reg_no) . '" 
                    class="btn btn-sm bg-success-light btn-add-mcu">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </td>
        ';

            $data_arr[] = [
                "no_registrasi"       => $record->reg_no,
                "nama_pasien"         => $record->nama ?? 'N/A',
                "eselon"              => $record->eselon ?? 'N/A',
                "tanggal_registrasi"      => \Carbon\Carbon::parse($record->tanggal_registrasi)->translatedFormat('d M Y'),
                "total_biaya_eselon"  => 'Rp ' . number_format($record->total_biaya_eselon, 0, ',', '.'),
                "deposit"             => 'Rp ' . number_format($record->deposit, 0, ',', '.'),
                "keterangan"          => $record->keterangan_batal
                    ? '<span class="badge bg-warning text-dark">' . $record->keterangan_batal . '</span>'
                    : '-',
                "modify"              => $modify,
            ];
        }

        return response()->json([
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"                 => $data_arr,
        ]);
    }
}
