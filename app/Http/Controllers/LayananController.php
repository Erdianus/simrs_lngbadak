<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LayananRequest;
use App\Models\Layanan;
use App\Service\LayananService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    public function index()
    {
        return view('layanan.index');
    }

    public function create(){
        return view('layanan.create');
    }

    public function store(LayananRequest $request){
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $createLayanan = LayananService::createLayanan($validatedData);
        if ($createLayanan) {
            Toastr::success('Layanan Berhasil Ditambahkan :)','Success');
            return redirect()->route('layanan/list');
        }
        Toastr::error('Gagal menambahkan Layanan. Silakan coba lagi.','Error');
        return redirect()->route('layanan/list');
    }

    public function edit($slug){
        $layanan = Layanan::where('slug',$slug)->first();
        return view('layanan.edit', compact('layanan'));
    }

    public function update(LayananRequest $request, $slug){
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $updateLayanan = LayananService::updateLayanan($slug, $validatedData);
        if ($updateLayanan) {
            Toastr::success('Layanan berhasil diperbaharui!', 'Success');
            return redirect()->route('layanan/list');
        }
        Toastr::error('Layanan gagal diperbaharui!', 'Error');
        return redirect()->route('layanan/list');
    }

    public function destroy(Request $request){
        $slug = $request->input('slug');
        $deleteLayanan = LayananService::deleteLayanan($slug);
        if ($deleteLayanan) {
            Toastr::success('Layanan berhasil dihapus!', 'Success');
            return redirect()->route('layanan/list');
        }
        Toastr::error('Layanan gagal dihapus!', 'Error');
        return redirect()->route('layanan/list');
    }

    /** get layanans data */
    public function getLayanansData(Request $request)
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

        $eslons =  DB::table('layanans');
        $totalRecords = $eslons->count();

        $totalRecordsWithFilter = Layanan::where(function ($query) use ($searchValue) {
            $query->where('nama', 'like', '%' . $searchValue . '%');
        })->count();

        if ($columnName == 'nama') {
            $columnName = 'nama';
        }
        $records = Layanan::orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('nama', 'like', '%' . $searchValue . '%');
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
                            <a class="dropdown-item" href="'.url('layanan/edit/'.$record->slug).'">
                                <i class="far fa-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="'.url('layanan/delete/'.$record->slug).'">
                            <i class="fas fa-trash-alt m-r-5"></i> Delete
                        </a>
                        </div>
                    </div>
                </td>
            ';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="'.url('layanan/edit/'.$record->slug).'" class="btn btn-sm bg-danger-light">
                            <i class="far fa-edit me-2"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="'.$record->slug.'" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>
                    </div>
                </td>
            ';
           
            $data_arr [] = [
                "nama"         => $record->nama,
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
}
