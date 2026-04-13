<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingRequest;
use App\Models\Billing;
use App\Models\Eslon;
use App\Models\Layanan;
use App\Models\SubLayanan;
use App\Service\BillingService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        return view('billing.index');
    }

    public function create()
    {
        $layanans = Layanan::get();
        $sub_layanans = SubLayanan::get();
        $eselons = Eslon::get();
        return view('billing.create', compact('layanans', 'sub_layanans', 'eselons'));
    }

    public function store(BillingRequest $request)
    {
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $createEslon = BillingService::createBilling($validatedData);
        if ($createEslon) {
            Toastr::success('Berhasil Menambahkan EBilling :)', 'Success');
            return redirect()->route('billing/list');
        }
        Toastr::error('Gagal menambahkan Billing. Silakan coba lagi.', 'Error');
        return redirect()->route('billing/list');
    }

    public function edit($slug)
    {
        $billing = Billing::where('slug', $slug)->first();
        return view('eslon.edit', compact('billing'));
    }

    public function update(BillingRequest $request, $slug)
    {
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $updateBilling = BillingService::updateBilling($slug, $validatedData);
        if ($updateBilling) {
            Toastr::success('Billing berhasil diperbaharui!', 'Success');
            return redirect()->route('billing/list');
        }
        Toastr::error('Gagal memperbarui Billing. Silakan coba lagi.', 'Error');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $slug = $request->input('slug');
        $deleteBilling = BillingService::deleteBilling($slug);
        if ($deleteBilling) {
            Toastr::success('Billing berhasil dihapus!', 'Success');
            return redirect()->back();
        }
        Toastr::error('Gagal menghapus Billing. Silakan coba lagi.', 'Error');
        return redirect()->back();
    }

    /** get billing data */
    public function getBillingsVerifikasiData(Request $request)
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

        $totalRecords = Billing::doesntHave('sp3')->count();

        $totalRecordsWithFilter = Billing::doesntHave('sp3')->where(function ($query) use ($searchValue) {
            $query->orWhereHas('eselon', function ($subQuery) use ($searchValue) {
                $subQuery->where('nama', 'like', '%' . $searchValue . '%');
            });
            $query->orWhereHas('layanan', function ($subQuery) use ($searchValue) {
                $subQuery->where('nama', 'like', '%' . $searchValue . '%');
            });
            $query->orWhereHas('sub_layanan', function ($subQuery) use ($searchValue) {
                $subQuery->where('nama', 'like', '%' . $searchValue . '%');
            });
            $query->orWhere('keterangan', 'like', '%' . $searchValue . '%');
            $query->orWhere('no_registrasi', 'like', '%' . $searchValue . '%');
        })->count();

        $records = Billing::with(['sp3', 'eselon', 'layanan', 'sub_layanan'])
            ->doesntHave('sp3')
            // Buka tabel relasi agar bisa dipakai orderBy
            ->leftJoin('eslons', 'eslons.id', '=', 'billings.eslon_id')
            ->leftJoin('layanans', 'layanans.id', '=', 'billings.layanan_id')
            ->leftJoin('sub_layanans', 'sub_layanans.id', '=', 'billings.sub_layanan_id')
            ->select('billings.*') // Ambil kolom billings saja, hindari bentrok nama kolom
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('eslons.nama', 'like', "%$searchValue%")
                    ->orWhere('layanans.nama', 'like', "%$searchValue%")
                    ->orWhere('sub_layanans.nama', 'like', "%$searchValue%")
                    ->orWhere('billings.keterangan', 'like', "%$searchValue%")
                    ->orWhere('billings.no_registrasi', 'like', "%$searchValue%");
            })
            ->orderBy($this->mapColumn($columnName), $columnSortOrder)
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
                            <a class="dropdown-item" href="' . url('billing/edit/' . $record->slug) . '">
                                <i class="far fa-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="' . url('billing/delete/' . $record->slug) . '">
                            <i class="fas fa-trash-alt m-r-5"></i> Delete
                        </a>
                        </div>
                    </div>
                </td>
            ';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="' . url('billing/edit/' . $record->slug) . '" class="btn btn-sm bg-danger-light">
                            <i class="far fa-edit me-2"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="' . $record->slug . '" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "sp3"         => $record->sp3->no_sp3 ?? 'N/A',
                "keterangan"    => $record->keterangan,
                "no_registrasi"    => $record->no_registrasi,
                "eslon"    => $record->eselon->nama ?? 'N/A',
                "layanan"    => $record->layanan->nama ?? 'N/A',
                "sub_layanan"    => $record->sub_layanan->nama ?? 'N/A',
                "biaya" => $record->biaya,
                "modify"       => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"               => $data_arr
        ];
        // dump($response);
        return response()->json($response);
    }


    public function getBillingsSp3Data(Request $request, $sp3_slug)
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

        $totalRecords = Billing::whereHas('sp3', function ($query) use ($sp3_slug) {
            $query->where('slug', $sp3_slug);
        })->count();

        $totalRecordsWithFilter = Billing::whereHas('sp3', function ($query) use ($sp3_slug) {
            $query->where('slug', $sp3_slug);
        })->where(function ($query) use ($searchValue) {
            $query->orWhereHas('eselon', function ($subQuery) use ($searchValue) {
                $subQuery->where('nama', 'like', '%' . $searchValue . '%');
            });
            $query->orWhereHas('layanan', function ($subQuery) use ($searchValue) {
                $subQuery->where('nama', 'like', '%' . $searchValue . '%');
            });
            $query->orWhereHas('sub_layanan', function ($subQuery) use ($searchValue) {
                $subQuery->where('nama', 'like', '%' . $searchValue . '%');
            });
            $query->orWhere('keterangan', 'like', '%' . $searchValue . '%');
            $query->orWhere('no_registrasi', 'like', '%' . $searchValue . '%');
        })->count();

        $records = Billing::with(['sp3', 'eselon', 'layanan', 'sub_layanan'])
            ->whereHas('sp3', function ($query) use ($sp3_slug) {
                $query->where('slug', $sp3_slug);
            })
            // Buka tabel relasi agar bisa dipakai orderBy
            ->leftJoin('sp3s', 'sp3s.id', '=', 'billings.sp3_id')
            ->leftJoin('eslons', 'eslons.id', '=', 'billings.eslon_id')
            ->leftJoin('layanans', 'layanans.id', '=', 'billings.layanan_id')
            ->leftJoin('sub_layanans', 'sub_layanans.id', '=', 'billings.sub_layanan_id')
            ->select('billings.*') // Ambil kolom billings saja, hindari bentrok nama kolom
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('sp3s.no_sp3', 'like', "%$searchValue%")
                    ->orWhere('eslons.nama', 'like', "%$searchValue%")
                    ->orWhere('layanans.nama', 'like', "%$searchValue%")
                    ->orWhere('sub_layanans.nama', 'like', "%$searchValue%")
                    ->orWhere('billings.keterangan', 'like', "%$searchValue%")
                    ->orWhere('billings.no_registrasi', 'like', "%$searchValue%");
            })
            ->orderBy($this->mapColumn($columnName), $columnSortOrder)
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
                            <a class="dropdown-item" href="' . url('billing/edit/' . $record->slug) . '">
                                <i class="far fa-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="' . url('billing/delete/' . $record->slug) . '">
                            <i class="fas fa-trash-alt m-r-5"></i> Delete
                        </a>
                        </div>
                    </div>
                </td>
            ';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="' . url('billing/edit/' . $record->slug) . '" class="btn btn-sm bg-danger-light">
                            <i class="far fa-edit me-2"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="' . $record->slug . '" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "sp3"         => $record->sp3->no_sp3 ?? 'N/A',
                "keterangan"    => $record->keterangan,
                "no_registrasi"    => $record->no_registrasi,
                "eslon"    => $record->eselon->nama ?? 'N/A',
                "layanan"    => $record->layanan->nama ?? 'N/A',
                "sub_layanan"    => $record->sub_layanan->nama ?? 'N/A',
                "biaya" => $record->biaya,
                "modify"       => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"               => $data_arr
        ];
        // dump($response);
        return response()->json($response);
    }

    private function mapColumn($columnName)
    {
        $map = [
            'sp3'         => 'sp3s.no_sp3',           // tabel: sp3s
            'eslon'       => 'eslons.nama',            // tabel: eslons
            'layanan'     => 'layanans.nama',          // tabel: layanans
            'sub_layanan' => 'sub_layanans.nama',      // tabel: sub_layanans
            'keterangan'  => 'billings.keterangan',
            'no_registrasi' => 'billings.no_registrasi',
            'biaya'       => 'billings.biaya',
        ];
        return $map[$columnName] ?? $columnName;
    }
}
