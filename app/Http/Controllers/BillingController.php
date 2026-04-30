<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingRequest;
use App\Models\Billing;
use App\Models\Eslon;
use App\Models\Layanan;
use App\Models\Simrs\KipKirimanSimrs;
use App\Models\Simrs\ReferensiAdmSimrs;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Simrs\TindakanSimrs;
use App\Models\Simrs\TransaksiAlkesSimrs;
use App\Models\Simrs\TransaksiEmbalaceSimrs;
use App\Models\Simrs\TransaksiKamarSimrs;
use App\Models\Simrs\TransaksiResepSimrs;
use App\Models\SubLayanan;
use App\Service\BillingService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

    public function listTindakanBill($bill)
    {
        $billing = Billing::where('no_registrasi', $bill)->first();
        $tindakan = TindakanSimrs::select(['reg_no', 'jumlah', 'discount', 'payment', 'tindakan_id', 'tindakan_biaya'])
            ->where('reg_no', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $alkes = TransaksiAlkesSimrs::select(['reg_no', 'discount', 'payment', 'jumlah_jual', 'alkes_id', 'harga_jual'])
            ->where('reg_no', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        // dd($alkes);
        $resepRawatJalan = TransaksiResepSimrs::select(['regnum', 'jumlah_dijual', 'harga_jual', 'discount', 'payment', 'farmalkes_id'])
            ->where('regnum', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        // dd($resepRawatJalan);
        $resepRawatInap = KipKirimanSimrs::select(['no_reg', 'farmalkes_id', 'jumlah_kiriman', 'kiriman_id', 'payment', 'harga', 'discount'])
            ->where('no_reg', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $kamar = TransaksiKamarSimrs::select(['no_reg', 'id_kamar', 'lama_hari', 'keterangan', 'tarif_sewa', 'discount'])
            ->where('no_reg', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $embalace = TransaksiEmbalaceSimrs::select(['no_reg', 'ppn', 'discount'])
            ->where('no_reg', $billing->no_registrasi)
            ->where('payment', NULL)
            ->get();
        $dataBiayaAdm = [];
        $totalEselon = ($tindakan->sum('total_biaya')) + ($alkes->sum('total_biaya')) + ($resepRawatJalan->sum('total_biaya')) + ($resepRawatInap->sum('total_biaya')) + ($kamar->sum('total_biaya')) + ($embalace->sum('ppn'));
        if ($kamar->count() > 0 || $resepRawatInap->count() > 0) {
            $ref_adm = ReferensiAdmSimrs::select(['besar_fee', 'max_besar'])->where('kode_eselon', $billing->eselon->nama)->first();
            $biayaAdm = ceil(($ref_adm->besar_fee / 100) * $totalEselon);
            if ($biayaAdm > $ref_adm->max_besar) {
                $biayaAdm = $ref_adm->max_besar;
            }
            $dataBiayaAdm = [
                'nama_tindakan' => $ref_adm->deskripsi,
                'jumlah' => 1,
                'biaya' => $biayaAdm,
            ];
        }

        return view('tindakan.detail-list-tindakan', compact('billing', 'tindakan', 'alkes', 'resepRawatInap', 'resepRawatJalan', 'kamar', 'embalace', 'dataBiayaAdm'));
    }

    public function store(BillingRequest $request)
    {
        // Validasi data yang diterima dari form
        $validatedData = $request->validated();
        $createEslon = BillingService::createBilling($validatedData);
        if ($createEslon) {
            Toastr::success('Berhasil Menambahkan Billing :)', 'Success');
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

    public function approveBill($slug)
    {
        $approveBilling = BillingService::approveBill($slug);
        if ($approveBilling) {
            Toastr::success('Billing berhasil disetujui!', 'Success');
            return redirect()->back();
        }
        Toastr::error('Gagal menyetujui Billing. Silakan coba lagi.', 'Error');
        return redirect()->back();
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
            $query->orWhere('nama_pasien', 'like', '%' . $searchValue . '%');
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
                    ->orWhere('billings.no_registrasi', 'like', "%$searchValue%")
                    ->orWhere('billings.nama_pasien', 'like', "%$searchValue%");
            })
            ->where('no_registrasi', 'like', "%$searchValue%")
            ->orderBy($this->mapColumn($columnName), $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();
        $data_arr = [];
        $userId = auth()->id();
        $role = auth()->user()->role_name;
        foreach ($records as $key => $record) {
            $status = $record->is_verified_by_verifikator ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-secondary">Belum Terverifikasi</span>';
            $modify = '<td class="text-right">
                <div class="dropdown dropdown-action">
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="' . url('/detail-billing/' . $record->no_registrasi) . '">
                            <i class="far fa-eye me-2"></i> Detail
                        </a>
                        ' . (!$record->is_verified_by_verifikator ? '
                        <a class="dropdown-item" href="' . url('/billing/approve/' . $record->no_registrasi) . '">
                            <i class="fa fa-check me-2"></i> Approve
                        </a>' : '') . '
                    </div>
                </div>
            </td>';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="' . url('detail-billing/' . $record->no_registrasi) . '" class="btn btn-sm bg-success-light">
                            <i class="far fa-eye me-2"></i>
                        </a>
                        ' . ($record->is_verified_by_verifikator != true ? '
                        <a href="' . url('/billing/approve/' . $record->no_registrasi) . '" class="btn btn-sm bg-success-light">
                            <i class="fa fa-check me-2"></i>
                        </a>' : '') . '
                    </div>
                </td>
            ';

            $data_arr[] = [
                "sp3"         => $record->sp3->no_sp3 ?? 'N/A',
                "no_registrasi"    => $record->no_registrasi,
                "nama_pasien"    => $record->nama_pasien ?? 'N/A',
                "eslon"    => $record->eselon->deskripsi ?? 'N/A',
                "total_tindakan"    => 'Rp ' . number_format($record->total_tindakan, 0, ',', '.'),
                "total_BMHP"    => 'Rp ' . number_format($record->total_BMHP, 0, ',', '.'),
                "total_resep"    => 'Rp ' . number_format($record->total_resep, 0, ',', '.'),
                "total_KIP"    => 'Rp ' . number_format($record->total_KIP, 0, ',', '.'),
                "total_sewa_kamar"    => 'Rp ' . number_format($record->total_sewa_kamar, 0, ',', '.'),
                "total_PPN"    => 'Rp ' . number_format($record->total_ppn, 0, ',', '.'),
                "total_biaya_eselon"    => 'Rp ' . number_format($record->total_biaya_eselon, 0, ',', '.'),
                "total_biaya_kas"    => 'Rp ' . number_format($record->total_biaya_kas, 0, ',', '.'),
                "is_verified_by_verifikator"  => $record->is_verified_by_verifikator,
                "status" => $status,
                "keterangan" => $record->keterangan ? '<span class="badge bg-warning">' . $record->keterangan . '</span>' : '-',
                "modify" => $modify,
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
            'nama'        => 'billings.nama',
            'biaya'       => 'billings.biaya',
        ];
        return $map[$columnName] ?? $columnName;
    }
    // <a class="dropdown-item" href="' . url('billing/edit/' . $record->slug) . '">
    //                             <i class="far fa-edit me-2"></i> Edit
    //                         </a>
    //                         <a class="dropdown-item" href="' . url('billing/delete/' . $record->slug) . '">
    //                         <i class="fas fa-trash-alt m-r-5"></i> Delete
    // <a href="' . url('billing/edit/' . $record->slug) . '" class="btn btn-sm bg-danger-light">
    //                         <i class="far fa-edit me-2"></i>
    //                     </a>
    //                     <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="' . $record->slug . '" data-bs-target="#delete">
    //                     <i class="fe fe-trash-2"></i>
    //                     </a>
}
