<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingRequest;
use App\Models\Billing;
use App\Models\Eslon;
use App\Models\Layanan;
use App\Models\SubLayanan;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Service\BillingService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BillingController extends Controller
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

        // $reg = RegMultiPoliSimrs::selectRaw("distinct
        //     date(reg_multi_poli.tanggal_registrasi) as TGL_REGISTRASI,
        //     dateformat(reg_multi_poli.tanggal_registrasi, 'hh:mm:ss') as JAM_REGISTRASI,
        //     case when reg_multi_poli.jadi = 'Y' then 'BATAL' end as BATAL,
        //     case when reg_multi_poli.pasien_baru = 'Y' then 'BARU' when reg_multi_poli.pasien_baru = 'N' then 'LAMA' end as KUNJUNGAN,
        //     reg_multi_poli.no_mr as NO_MR,
        //     reg_multi_poli.no_pegawai as NO_PEGAWAI,
        //     reg_multi_poli.reg_no as NO_REG,
        //     reg_multi_poli.nama as NAMA,
        //     case when reg_multi_poli.kelamin = 'P' then 'PEREMPUAN' when reg_multi_poli.kelamin = 'L' then 'LAKI - LAKI' end as KELAMIN,
        //     trans_kamar.tanggal_masuk as TANGGAL_MASUK_RANAP,
        //     trans_kamar.tanggal_keluar as TANGGAL_KELUAR_RANAP,
        //     pasien.golongan_darah as GOL_DARAH,
        //     pasien.agama as AGAMA,
        //     pasien.warga_negara as WARGA_NEGARA,
        //     pasien.status as STS,
        //     pasien.pekerjaan as PEKERJAAN,
        //     pasien.jabatan as JABATAN,
        //     pasien.kode_perusahaan as PRSHN_KERJA,
        //     pasien.ayah as AYAH,
        //     pasien.ibu as IBU,
        //     pasien.alamat as ALAMAT,
        //     pasien.kelurahan as KELURAHAN,
        //     pasien.rt as RT,
        //     pasien.rw as RW,
        //     kecamatan.nama_kecamatan as KECAMATAN,
        //     kabupaten.nama_kabupaten as KABUPATEN,
        //     propinsi.nama_propinsi as PROPINSI,
        //     pasien.kode_pos as KODE_POS,
        //     pasien.tempat_lahir as TPT_LAHIR,
        //     reg_multi_poli.tgl_lahir as TGL_LAHIR,
        //     reg_multi_poli.kode_poli as POLI_ID,
        //     master_poli.poli_name as NAMA_POLI,
        //     reg_multi_poli.kode_kamar as KAMAR,
        //     reg_multi_poli.penanggung_nama as NAMA_PENANGGUNG,
        //     reg_multi_poli.penanggung_status as STATUS_PENANGGUNG,
        //     reg_multi_poli.penanggung_alamat as ALAMAT_PENANGGUNG,
        //     reg_multi_poli.penanggung_telpon as TELPON_PENANGGUNG,
        //     reg_multi_poli.asal_pasien as ASAL_PASIEN,
        //     reg_multi_poli.kasus_polisi as KASUS_POLISI,
        //     reg_multi_poli.kasus_kesehatan as KASUS_KESEHATAN,
        //     reg_multi_poli.no_telp as No_TELP,
        //     reg_multi_poli.status as STS_KELUARGA,
        //     ku_kode_eselon.deskripsi as ESELON,
        //     ku_kode_eselon_group.deskripsi as GRUP_ESELON,
        //     f_nama_dokter(reg_multi_poli.dokter_id) as DOKTER,
        //     list(distinct case when transaksi_icd.tipe = 1 then transaksi_icd.icd_id end) as ICD_PRIMER,
        //     list(distinct case when transaksi_icd.tipe = 2 then transaksi_icd.icd_id end) as ICD_SEKUNDER,
        //     list(distinct case when transaksi_icd.tipe = 3 then transaksi_icd.icd_id end) as ICD_TERTIER,
        //     list(distinct upper(transaksi_icd.icd_id)) as ICD,
        //     list(distinct icd.icd_desc) as ICD_DESC,
        //     reg_multi_poli.sep as SEP,
        //     pasien.no_peserta as NO_BPJS,
        //     pasien.no_ktp as NO_KTP,
        //     pasien.no_polis as NO_POLIS,
        //     pasien.no_penjamin as NO_PENJAMIN,
        //     pasien.no_asuransi as COST_CENTER,
        //     pasien.masa_berlaku as MASA_BERLAKU")
        //     ->leftJoin('pasien', 'reg_multi_poli.no_mr', '=', 'pasien.medrec_no')
        //     ->leftJoin('kecamatan', 'pasien.kecamatan', '=', 'kecamatan.kecamatan_id')
        //     ->leftJoin('kabupaten', 'pasien.kabupaten', '=', 'kabupaten.kabupaten_id')
        //     ->leftJoin('propinsi', 'pasien.propinsi', '=', 'propinsi.propinsi_id')
        //     ->leftJoin('trans_kamar', 'reg_multi_poli.reg_no', '=', 'trans_kamar.no_reg')
        //     ->leftJoin('transaksi_icd', 'reg_multi_poli.reg_no', '=', 'transaksi_icd.reg_no')
        //     ->leftJoin('icd', 'icd.icd_id', '=', 'transaksi_icd.icd_id')
        //     ->join('ku_kode_eselon', 'ku_kode_eselon.kode_eselon', '=', 'reg_multi_poli.eselon')
        //     ->join('ku_kode_eselon_group', 'ku_kode_eselon_group.kode_group', '=', 'ku_kode_eselon.kode_group')
        //     ->join('master_poli', 'master_poli.poli_id', '=', 'reg_multi_poli.kode_poli')
        //     ->whereIn('reg_multi_poli.kode_poli', $this->kode_poli)
        //     ->whereRaw("date(reg_multi_poli.tanggal_registrasi) between ? and ?", ['2026-03-27', '2026-04-05'])
        //     ->groupBy([
        //         'reg_multi_poli.tanggal_registrasi',
        //         'ku_kode_eselon.deskripsi',
        //         'ku_kode_eselon_group.deskripsi',
        //         'reg_multi_poli.no_mr',
        //         'reg_multi_poli.reg_no',
        //         'reg_multi_poli.no_pegawai',
        //         'reg_multi_poli.nama',
        //         'reg_multi_poli.kode_kamar',
        //         'trans_kamar.tanggal_masuk',
        //         'trans_kamar.tanggal_keluar',
        //         'reg_multi_poli.penanggung_nama',
        //         'reg_multi_poli.penanggung_status',
        //         'reg_multi_poli.penanggung_alamat',
        //         'reg_multi_poli.penanggung_telpon',
        //         'pasien.golongan_darah',
        //         'pasien.tempat_lahir',
        //         'pasien.agama',
        //         'pasien.warga_negara',
        //         'pasien.status',
        //         'pasien.pekerjaan',
        //         'pasien.jabatan',
        //         'reg_multi_poli.asal_pasien',
        //         'reg_multi_poli.kasus_polisi',
        //         'reg_multi_poli.kasus_kesehatan',
        //         'reg_multi_poli.status',
        //         'reg_multi_poli.no_telp',
        //         'reg_multi_poli.kelamin',
        //         'reg_multi_poli.kode_poli',
        //         'master_poli.poli_name',
        //         'reg_multi_poli.jadi',
        //         'reg_multi_poli.pasien_baru',
        //         'reg_multi_poli.tgl_lahir',
        //         'pasien.kelurahan',
        //         'pasien.rt',
        //         'pasien.rw',
        //         'kecamatan.nama_kecamatan',
        //         'kabupaten.nama_kabupaten',
        //         'propinsi.nama_propinsi',
        //         'pasien.kode_pos',
        //         'reg_multi_poli.sep',
        //         'pasien.no_peserta',
        //         'pasien.no_ktp',
        //         'pasien.no_polis',
        //         'pasien.no_penjamin',
        //         'pasien.no_asuransi',
        //         'pasien.masa_berlaku',
        //         'pasien.kode_perusahaan',
        //         'pasien.ayah',
        //         'pasien.ibu',
        //         'pasien.alamat',
        //         'reg_multi_poli.dokter_id'
        //     ])
        //     ->orderBy('reg_multi_poli.reg_no')
        //     ->get();



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
