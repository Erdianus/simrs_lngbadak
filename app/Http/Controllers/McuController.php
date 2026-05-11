<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Sp3;
use Illuminate\Http\Request;

class McuController extends Controller
{
    public function getRegMcuData(Request $request, $sp3_slug)
    {
        $sp3 = Sp3::where('slug', $sp3_slug)->first();
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

        // ✅ Whitelist kolom yang boleh di-order
        $validColumns = ['reg_no', 'nama', 'tanggal_registrasi'];
        if (!in_array($columnName, $validColumns)) {
            $columnName = 'tanggal_registrasi';
        }
        $columnSortOrder = in_array(strtolower($columnSortOrder), ['asc', 'desc'])
            ? $columnSortOrder
            : 'desc';

        // ✅ Base query — reusable
        $baseQuery = RegMultiPoliSimrs::whereIn('kode_poli', ['MCU01', 'LAB01'])
            ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$sp3->tgl_masuk, $sp3->tgl_keluar])
            ->where('eselon', $sp3->eselon->nama);

        // ✅ Total tanpa filter search
        $totalRecords = (clone $baseQuery)->count();

        // ✅ Total dengan filter search (konsisten dengan $records)
        $totalRecordsWithFilter = (clone $baseQuery)
            ->where(function ($query) use ($searchValue) {
                $query->where('reg_no', 'like', '%' . $searchValue . '%')
                    ->orWhere('nama', 'like', '%' . $searchValue . '%')
                    ->orWhere('tanggal_registrasi', 'like', '%' . $searchValue . '%');
            })
            ->count();

        // ✅ Records dengan semua filter
        $records = (clone $baseQuery)
            ->where(function ($query) use ($searchValue) {
                $query->where('reg_no', 'like', '%' . $searchValue . '%')
                    ->orWhere('nama', 'like', '%' . $searchValue . '%')
                    ->orWhere('tanggal_registrasi', 'like', '%' . $searchValue . '%');
            })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();

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
                "reg_no"             => $record->reg_no,
                "nama"               => $record->nama ?? 'N/A',
                "eselon"             => $record->eselon ?? 'N/A',
                "tanggal_registrasi" => \Carbon\Carbon::parse($record->tanggal_registrasi)->translatedFormat('d M Y'),
                "total_biaya_eselon" => 'Rp ' . number_format($record->total_biaya_eselon, 0, ',', '.'),
                "deposit"            => 'Rp ' . number_format($record->deposit, 0, ',', '.'),
                "keterangan"         => $record->keterangan_batal
                    ? '<span class="badge bg-warning text-dark">' . $record->keterangan_batal . '</span>'
                    : '-',
                "modify"             => $modify,
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
