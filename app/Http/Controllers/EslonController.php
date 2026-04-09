<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EslonRequest;
use App\Models\Eslon;
use App\Service\EslonService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EslonController extends Controller
{
    public function index()
    {
        return view('eslon.index');
    }

    public function create(){
        return view('eslon.create');
    }

    public function store(EslonRequest $request){
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $createEslon = EslonService::createEslon($validatedData);
        if ($createEslon) {
            Toastr::success('Berhasil Menambahkan Eselon :)','Success');
            return redirect()->route('eselon/list');
            }
            Toastr::error('Gagal menambahkan Eslon. Silakan coba lagi.','Error');
            return redirect()->route('eselon/list');
    }

    public function edit($slug){
        $eselon = Eslon::where('slug', $slug)->first();
        return view('eslon.edit', compact('eselon'));
    }

    public function update(EslonRequest $request, $slug){
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $updateEslon = EslonService::updateEslon($slug, $validatedData);
        if ($updateEslon) {
            Toastr::success('Eselon berhasil diperbaharui!', 'Success');
            return redirect()->route('eselon/list');
        }
        Toastr::error('Gagal memperbarui Eslon. Silakan coba lagi.', 'Error');
        return redirect()->back();
    }

    public function destroy(Request $request){
        $slug = $request->input('slug');
        $deleteEslon = EslonService::deleteEslon($slug);
        if ($deleteEslon) {
            Toastr::success('Eslon berhasil dihapus!', 'Success');
            return redirect()->back();
        }
        Toastr::error('Gagal menghapus Eslon. Silakan coba lagi.', 'Error');
        return redirect()->back();
    }

    /** get eslon data */
    public function getEslonsData(Request $request)
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

        $eslons =  DB::table('eslons');
        $totalRecords = $eslons->count();

        $totalRecordsWithFilter = Eslon::where(function ($query) use ($searchValue) {
            $query->where('nama', 'like', '%' . $searchValue . '%');
            $query->orWhere('deskripsi', 'like', '%' . $searchValue . '%');
        })->count();

        if ($columnName == 'nama') {
            $columnName = 'nama';
        }
        $records = Eslon::orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('nama', 'like', '%' . $searchValue . '%');
                $query->orWhere('deskripsi', 'like', '%' . $searchValue . '%');
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
                            <a class="dropdown-item" href="'.url('eselon/edit/'.$record->slug).'">
                                <i class="far fa-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="'.url('eselon/delete/'.$record->slug).'">
                            <i class="fas fa-trash-alt m-r-5"></i> Delete
                        </a>
                        </div>
                    </div>
                </td>
            ';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="'.url('eselon/edit/'.$record->slug).'" class="btn btn-sm bg-danger-light">
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
                "deskripsi"    => $record->deskripsi,
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
