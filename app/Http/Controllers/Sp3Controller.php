<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sp3Request;
use App\Models\Eslon;
use App\Models\Layanan;
use App\Models\PerihalTagihan;
use App\Models\Simrs\EselonSimrs;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Sp3;
use App\Service\Sp3Service;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Sp3Controller extends Controller
{
    private $kode_poli = [
        'MCU01',
        'RJ002',
        'RJ004',
        'RJ006',
        'RJ008',
        'RJ010',
        'RJ012',
        'RJ014',
        'RJ016',
        'RJ018',
        'FIS01',
        'RIN01',
        'LAB01',
        'ADM02',
        'HC',
        'RJ021',
        'RJ023',
        'TND01',
        'RJ025',
        'RJ027',
        'RJ029',
        'RJ030',
        'TER01',
        'RJ034',
        'CSSD',
        'RJ001',
        'RJ003',
        'RJ005',
        'RJ007',
        'RJ009',
        'RJ011',
        'RJ013',
        'RJ015',
        'RJ017',
        'RJ019',
        'FAR01',
        'IGD01',
        'OK001',
        'RAD01',
        'RJ020',
        'RJ022',
        'RJ024',
        'RJ026',
        'RJ028',
        'RJ031',
        'RJ032',
        'RJ033',
        'RJ035'
    ];
    public function index()
    {
        return view('sp3.index');
    }

    public function create()
    {
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->get();
        return view('sp3.create', compact('kode_tagihan', 'eselon', 'layanan'));
    }

    public function store(Sp3Request $request)
    {
        $validated = $request->validated();
        // dd($validated);
        $tglMasuk  = Carbon::createFromFormat('d-m-Y', $validated['tgl_masuk'])->format('Y-m-d');
        $tglKeluar  = Carbon::createFromFormat('d-m-Y', $validated['tgl_keluar'])->format('Y-m-d');
        $eslon = Eslon::findOrFail($validated['eslon_id']);
        $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon'])
            ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
            ->whereIn('kode_poli', $this->kode_poli)
            ->where('eselon', $eslon->nama)
            ->get();
        if ($getDataReg->isEmpty()) {
            Toastr::error('Data Billing Tidak Ada', 'Error');
            return redirect()->back();
        }
        $create = Sp3Service::createSp3($validated, $getDataReg, $eslon);
        if ($create === true) {
            Toastr::success('Berhasil Menambahkan SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        }

        Toastr::error($create->getMessage(), 'Error');
        return redirect()->back();
    }

    public function listBillSp3($sp3_slug)
    {
        $sp3 = Sp3::where('slug', $sp3_slug)->first();
        return view('sp3.detail-list-bill', compact('sp3'));
    }


    /** get sp3 data */
    public function getSp3VerifikasiData(Request $request)
    {
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column']; // Column index
        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $totalRecords = Sp3::count();

        $totalRecordsWithFilter = Sp3::where('is_approved_by_verifikator', false)
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('no_sp3', 'like', '%' . $searchValue . '%');
                $query->orWhere('nomor_tagihan', 'like', '%' . $searchValue . '%');
                $query->orWhere('ket_inv_pasien', 'like', '%' . $searchValue . '%');
                $query->orWhere('ket_inv_rs', 'like', '%' . $searchValue . '%');
                $query->orWhere('ket_pembayaran', 'like', '%' . $searchValue . '%');
                $query->orWhere('kota', 'like', '%' . $searchValue . '%');
                $query->orWhere('nama_rs', 'like', '%' . $searchValue . '%');
                $query->orWhere('dokter_rujukan', 'like', '%' . $searchValue . '%');
            })->count();

        $records = Sp3::with('eselon')->orderBy($columnName, $columnSortOrder)
            ->where('is_approved_by_verifikator', false)
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('no_sp3', 'like', '%' . $searchValue . '%');
                $query->orWhere('nomor_tagihan', 'like', '%' . $searchValue . '%');
                $query->orWhere('ket_inv_pasien', 'like', '%' . $searchValue . '%');
                $query->orWhere('ket_inv_rs', 'like', '%' . $searchValue . '%');
                $query->orWhere('ket_pembayaran', 'like', '%' . $searchValue . '%');
                $query->orWhere('kota', 'like', '%' . $searchValue . '%');
                $query->orWhere('nama_rs', 'like', '%' . $searchValue . '%');
                $query->orWhere('dokter_rujukan', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();
        $data_arr = [];

        foreach ($records as $key => $record) {
            $modify = '
                <td class="text-right">
                    <div class="dropdown dropdown-action">
                        <a href="" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v ellipse_color"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="' . url('sp3/edit/' . $record->slug) . '">
                                <i class="far fa-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="' . url('sp3/detail/' . $record->slug) . '">
                                <i class="far fa-eye me-2"></i> Detail
                            </a>
                            <a class="dropdown-item" href="' . url('sp3/delete/' . $record->slug) . '">
                            <i class="fas fa-trash-alt m-r-5"></i> Delete
                        </a>
                        </div>
                    </div>
                </td>
            ';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="' . url('sp3/detail/' . $record->slug) . '" class="btn btn-sm bg-success-light">
                            <i class="far fa-eye me-2"></i>
                        </a>
                        <a href="' . url('sp3/edit/' . $record->slug) . '" class="btn btn-sm bg-danger-light">
                            <i class="far fa-edit me-2"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="' . $record->slug . '" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "no_sp3"         => $record->no_sp3 ?? '-',
                "tgl_sp3"     => $record->tgl_sp3,
                "nomor_tagihan"    => $record->nomor_tagihan,
                "tgl_terima_keu"    => $record->tgl_terima_keu,
                "perihal_tagihan"    => $record->perihalTagihan->kode,
                "ket_inv_pasien"    => $record->ket_inv_pasien,
                "ket_inv_rs"    => $record->ket_inv_rs,
                "eselon"    => $record->eselon->nama,
                "jumlah_pasien"    => $record->total_pasien,
                "jumlah_kunjungan"    => $record->total_kunjungan,
                "ket_pembayaran"    => $record->ket_pembayaran,
                "layanan"    => $record->layanan->nama,
                "tgl_masuk"     => $record->tgl_masuk,
                "tgl_keluar"     => $record->tgl_keluar,
                "total_biaya"    => 'Rp ' . number_format($record->total_biaya, 0, ',', '.'),
                "modify"         => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"               => $data_arr
        ];
        return response()->json($response);
    }
}
