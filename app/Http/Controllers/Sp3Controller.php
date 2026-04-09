<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sp3;
use Illuminate\Http\Request;

class Sp3Controller extends Controller
{
    public function index()
    {
        return view('sp3.index');
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

        $totalRecordsWithFilter = Sp3::where(function ($query) use ($searchValue) {
            $query->orWhere('no_sp3', 'like', '%' . $searchValue . '%');
            $query->orWhere('keterangan', 'like', '%' . $searchValue . '%');
        })->count();

        $records = Sp3::orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('no_sp3', 'like', '%' . $searchValue . '%');
                $query->orWhere('keterangan', 'like', '%' . $searchValue . '%');
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
                "no_sp3"         => $record->no_sp3,
                "keterangan"     => $record->keterangan,
                "total_biaya"    => $record->total_biaya,
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
