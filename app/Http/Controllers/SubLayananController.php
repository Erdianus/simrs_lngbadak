<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubLayananRequest;
use App\Models\Layanan;
use App\Models\SubLayanan;
use App\Service\SubLayananService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubLayananController extends Controller
{
    public function index()
    {
        return view('sub-layanan.index');
    }

    public function create(){
        $layanans = Layanan::select(['id', 'nama'])->get();
        return view('sub-layanan.create', compact('layanans'));
    }

    public function store(SubLayananRequest $request){
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $createSubLayanan = SubLayananService::createSubLayanan($validatedData);
        if ($createSubLayanan) {
            Toastr::success('Sub Layanan Berhasil Ditambahkan :)','Success');
            return redirect()->route('sub-layanan/list');
        }
        Toastr::error('Gagal menambahkan sub Layanan. Silakan coba lagi.','Error');
        return redirect()->route('sub-layanan/list');
    }

    public function edit($slug){
        $subLayanan = SubLayanan::where('slug',$slug)->first();
        $layanans = Layanan::select(['id', 'nama'])->get();
        return view('sub-layanan.edit', compact('subLayanan', 'layanans'));
    }

    public function update(SubLayananRequest $request, $slug){
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $updateSubLayanan = SubLayananService::updateSubLayanan($slug, $validatedData);
        if ($updateSubLayanan) {
            Toastr::success('Sub Layanan berhasil diperbaharui!', 'Success');
            return redirect()->route('sub-layanan/list');
        }
        Toastr::error('Sub Layanan gagal diperbaharui!', 'Error');
        return redirect()->route('sub-layanan/list');
    }

    public function destroy(Request $request){
        $slug = $request->input('slug');
        $deleteSubLayanan = SubLayananService::deleteSubLayanan($slug);
        if ($deleteSubLayanan) {
            Toastr::success('Sub Layanan berhasil dihapus!', 'Success');
            return redirect()->route('sub-layanan/list');
        }
        Toastr::error('Sub Layanan gagal dihapus!', 'Error');
        return redirect()->route('sub-layanan/list');
    }

    /** get sub layanans data */
    public function getSubLayanansData(Request $request)
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

        $eslons =  DB::table('sub_layanans');
        $totalRecords = $eslons->count();

        $totalRecordsWithFilter = SubLayanan::where(function ($query) use ($searchValue) {
            $query->where('nama', 'like', '%' . $searchValue . '%');
        })->count();

        if ($columnName == 'nama') {
            $columnName = 'nama';
        }
        $records = SubLayanan::with('layanan')
            ->orderBy($columnName, $columnSortOrder)
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
                            <a class="dropdown-item" href="'.url('sub-layanan/edit/'.$record->slug).'">
                                <i class="far fa-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="'.url('sub-layanan/delete/'.$record->slug).'">
                            <i class="fas fa-trash-alt m-r-5"></i> Delete
                        </a>
                        </div>
                    </div>
                </td>
            ';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="'.url('sub-layanan/edit/'.$record->slug).'" class="btn btn-sm bg-danger-light">
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
                "layanan"      => $record->layanan->nama ?? 'N/A',
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
