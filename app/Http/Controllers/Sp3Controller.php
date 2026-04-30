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
use Barryvdh\DomPDF\Facade\Pdf;
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
        $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
            ->with('masterPoli')
            ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
            ->whereIn('kode_poli', $this->kode_poli)
            ->where('eselon', $eslon->nama)
            ->get()
            ->groupBy('reg_no')
            ->filter(function ($group) {
                // Jika reg_no hanya muncul 1 kali DAN jadi nilainya 'Y' → buang
                if ($group->count() === 1) {
                    $nilai = $group->first()->jadi; // ganti 'nama_kolom' dengan nama kolom aslinya
                    if ($nilai === 'Y') {
                        return false; // buang data ini
                    }
                }
                return true; // ambil data ini
            })
            ->map(function ($group) {
                $dataBatal = $group->where('jadi', 'Y');
                $adaBatal  = $group->count() > 1 && $dataBatal->count() > 0;

                $dataUtama = $group->where('jadi', '!=', 'Y')->first() ?? $group->first();

                if ($adaBatal) {
                    $jumlahBatal = $dataBatal->count();

                    // Ambil poli_name dari relasi masterPoli
                    $namaPoli = $dataBatal->map(fn($item) => $item->masterPoli?->poli_name ?? $item->kode_poli);

                    if ($jumlahBatal === 1) {
                        $dataUtama->keterangan_batal = "Memiliki registrasi batal pada poli: {$namaPoli->first()}";
                    } else {
                        $daftarPoli = $namaPoli->map(fn($poli, $i) => ($i + 1) . ". {$poli}")->implode(', ');
                        $dataUtama->keterangan_batal = "Memiliki {$jumlahBatal} registrasi batal pada poli: {$daftarPoli}";
                    }
                } else {
                    $dataUtama->keterangan_batal = null;
                }

                return $dataUtama;
            })
            ->flatten()
            ->unique('reg_no');
        $billing = $getDataReg->unique('reg_no');
        if ($getDataReg->isEmpty()) {
            Toastr::error('Data Billing Tidak Ada', 'Error');
            return redirect()->back();
        }
        $create = Sp3Service::createSp3($validated, $billing, $eslon);
        if ($create === true) {
            Toastr::success('Berhasil Menambahkan SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        }

        Toastr::error($create->getMessage(), 'Error');
        return redirect()->back();
    }

    public function edit($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->get();
        return view('sp3.edit', compact('kode_tagihan', 'eselon', 'layanan', 'sp3'));
    }

    public function update(Sp3Request $request, $slug)
    {
        $validated = $request->validated();
        // dd($validated);
        $tglMasuk  = Carbon::createFromFormat('d-m-Y', $validated['tgl_masuk'])->format('Y-m-d');
        $tglKeluar  = Carbon::createFromFormat('d-m-Y', $validated['tgl_keluar'])->format('Y-m-d');
        $eslon = Eslon::findOrFail($validated['eslon_id']);
        $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
            ->with('masterPoli')
            ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
            ->whereIn('kode_poli', $this->kode_poli)
            ->where('eselon', $eslon->nama)
            ->get()
            ->groupBy('reg_no')
            ->filter(function ($group) {
                // Jika reg_no hanya muncul 1 kali DAN jadi nilainya 'Y' → buang
                if ($group->count() === 1) {
                    $nilai = $group->first()->jadi; // ⭐ ganti 'nama_kolom' dengan nama kolom aslinya
                    if ($nilai === 'Y') {
                        return false; // buang data ini
                    }
                }
                return true; // ambil data ini
            })
            ->map(function ($group) {
                $dataBatal = $group->where('jadi', 'Y');
                $adaBatal  = $group->count() > 1 && $dataBatal->count() > 0;

                $dataUtama = $group->where('jadi', '!=', 'Y')->first() ?? $group->first();

                if ($adaBatal) {
                    $jumlahBatal = $dataBatal->count();

                    // Ambil poli_name dari relasi masterPoli
                    $namaPoli = $dataBatal->map(fn($item) => $item->masterPoli?->poli_name ?? $item->kode_poli);

                    if ($jumlahBatal === 1) {
                        $dataUtama->keterangan_batal = "Memiliki registrasi batal pada poli: {$namaPoli->first()}";
                    } else {
                        $daftarPoli = $namaPoli->map(fn($poli, $i) => ($i + 1) . ". {$poli}")->implode(', ');
                        $dataUtama->keterangan_batal = "Memiliki {$jumlahBatal} registrasi batal pada poli: {$daftarPoli}";
                    }
                } else {
                    $dataUtama->keterangan_batal = null;
                }

                return $dataUtama;
            })
            ->flatten()
            ->unique('reg_no');
        if ($getDataReg->isEmpty()) {
            Toastr::error('Data Billing Tidak Ada', 'Error');
            return redirect()->back();
        }
        $create = Sp3Service::updateSp3($validated, $getDataReg, $eslon, $slug);
        if ($create === true) {
            Toastr::success('Berhasil Mengupdate SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        }

        Toastr::error($create->getMessage(), 'Error');
        return redirect()->back();
    }

    public function updateDataBilling($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
            ->with('masterPoli')
            ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$sp3->tgl_masuk, $sp3->tgl_keluar])
            ->whereIn('kode_poli', $this->kode_poli)
            ->where('eselon', $sp3->eselon->nama)
            ->get()
            ->groupBy('reg_no')
            ->filter(function ($group) {
                // Jika reg_no hanya muncul 1 kali DAN jadi nilainya 'Y' → buang
                if ($group->count() === 1) {
                    $nilai = $group->first()->jadi; // ⭐ ganti 'nama_kolom' dengan nama kolom aslinya
                    if ($nilai === 'Y') {
                        return false; // buang data ini
                    }
                }
                return true; // ambil data ini
            })
            ->map(function ($group) {
                $dataBatal = $group->where('jadi', 'Y');
                $adaBatal  = $group->count() > 1 && $dataBatal->count() > 0;

                $dataUtama = $group->where('jadi', '!=', 'Y')->first() ?? $group->first();

                if ($adaBatal) {
                    $jumlahBatal = $dataBatal->count();

                    // Ambil poli_name dari relasi masterPoli
                    $namaPoli = $dataBatal->map(fn($item) => $item->masterPoli?->poli_name ?? $item->kode_poli);

                    if ($jumlahBatal === 1) {
                        $dataUtama->keterangan_batal = "Memiliki registrasi batal pada poli: {$namaPoli->first()}";
                    } else {
                        $daftarPoli = $namaPoli->map(fn($poli, $i) => ($i + 1) . ". {$poli}")->implode(', ');
                        $dataUtama->keterangan_batal = "Memiliki {$jumlahBatal} registrasi batal pada poli: {$daftarPoli}";
                    }
                } else {
                    $dataUtama->keterangan_batal = null;
                }
                return $dataUtama;
            })
            ->flatten()
            ->unique('reg_no');
        // dd($getDataReg);
        if ($getDataReg->isEmpty()) {
            Toastr::error('Data Billing Tidak Ada', 'Error');
            return redirect()->back();
        }
        $refresh = Sp3Service::refreshBillSp3($sp3, $getDataReg);
        if ($refresh === true) {
            Toastr::success('Berhasil Mengupdate SP3 :)', 'Success');
            return redirect()->back();
        }
        Toastr::error($refresh, 'Error');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $slug = $request->input('slug');
        $deleteBilling = Sp3Service::deleteSp3($slug);
        if ($deleteBilling) {
            Toastr::success('Sp3 berhasil dihapus!', 'Success');
            return redirect()->back();
        }
        Toastr::error('Gagal menghapus Sp3. Silakan coba lagi.', 'Error');
        return redirect()->back();
    }

    public function listBillSp3($sp3_slug)
    {
        $sp3 = Sp3::where('slug', $sp3_slug)->first();
        return view('sp3.detail-list-bill', compact('sp3'));
    }

    public function approveSp3($slug)
    {
        $sp3 = Sp3::with('billings')->where('slug', $slug)->first();

        // Ambil SP3 terakhir di tahun yang sedang berjalan
        $latestSp3 = Sp3::select('no_sp3')
            ->whereNotNull('no_sp3')
            ->whereYear('created_at', now()->year)
            ->latest()
            ->first();
        // Tentukan no_sp3 berikutnya
        if ($latestSp3) {
            // Masih tahun yang sama → lanjut +1
            $no_sp3 = $latestSp3->no_sp3 + 1;
        } else {
            // Tahun baru atau belum ada data → reset ke starter
            $no_sp3 = env('STARTER_SP3_NUMBER', 1);
        }
        $allVerified = $sp3->billings->every(fn($billing) => $billing->is_verified_by_verifikator == true);
        if (!$allVerified) {
            Toastr::error('Billing Sp3 ini belum semua disetujui.', 'Error');
            return redirect()->back();
        }
        $approveSp3 = Sp3Service::approveSp3($slug, $no_sp3);
        if ($approveSp3) {
            Toastr::success('Sp3 berhasil disetujui!', 'Success');
            return redirect()->back();
        }
        Toastr::error('Gagal menyetujui Sp3. Silakan coba lagi.', 'Error');
        return redirect()->back();
    }

    public function previewSp3($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        // dd($sp3);
        $data = [
            'nomor' => $sp3->no_surat_sp3,
            'tanggal' => \Carbon\Carbon::parse($sp3->tgl_sp3)->translatedFormat('d F Y'),
            'pasien' => $sp3->eselon->deskripsi,
            'tagihan' => $sp3->total_tagihan,
            'kunjungan' => $sp3->total_kunjungan,
            'hal' => $sp3->perihalTagihan->hal,
            'ket_pembayaran' => $sp3->perihalTagihan->ket_pembayaran,
            'disetujui_oleh' => 'dr. RIEN POTU AGUSTINA',
            'diketahui_oleh' => 'dr. PUTU ISMA SARASWATI DEWI',
            'dibuat_oleh' => auth()->user()->rolename != 'Super Admin' ? auth()->user()->name : '',
            'ttd_path' => ''
        ];

        $pdf = Pdf::loadView('pdf.sp3', [
            'data' => $data,
            'qr' => null,
        ])->setPaper('a5', 'portrait');

        // Preview di browser
        return $pdf->stream('preview-sp3.pdf');
    }

    public function generateNoSp3($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        if (!$sp3->no_sp3 != null) {
            Toastr::error('Sp3 sudah memiliki No Sp3.', 'Error');
            return redirect()->back();
        }
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
            $query->orWhere('nomor_tagihan', 'like', '%' . $searchValue . '%');
            $query->orWhere('ket_inv_pasien', 'like', '%' . $searchValue . '%');
            $query->orWhere('ket_inv_rs', 'like', '%' . $searchValue . '%');
            $query->orWhere('ket_pembayaran', 'like', '%' . $searchValue . '%');
            $query->orWhere('kota', 'like', '%' . $searchValue . '%');
            $query->orWhere('nama_rs', 'like', '%' . $searchValue . '%');
            $query->orWhere('dokter_rujukan', 'like', '%' . $searchValue . '%');
        })->count();

        $records = Sp3::with('eselon')->orderBy($columnName, $columnSortOrder)
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
            ->orderBy('is_approved_by_verifikator', 'DESC')
            ->get();
        $data_arr = [];

        foreach ($records as $key => $record) {
            $status = $record->is_approved_by_verifikator ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-secondary">Belum Terverifikasi</span>';
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
                            ' . (!$record->is_approved_by_verifikator ? '
                            <a class="dropdown-item" href="' . url('/sp3/approve/' . $record->slug) . '">
                                <i class="fa fa-check me-2"></i> Approve
                            </a>' : '') . '
                            ' . ($record->is_approved_by_verifikator ? '
                            <a class="dropdown-item" href="' . url('/sp3/' . $record->slug . '/preview') . '">
                                <i class="fa fa-print me-2"></i> Print
                            </a>' : '') . '
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
                        ' . ($record->is_approved_by_verifikator != true ? '
                        <a href="' . url('/sp3/approve/' . $record->slug) . '" class="btn btn-sm bg-success-light">
                            <i class="fa fa-check me-2"></i>
                        </a>' : '') . '
                        <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="' . $record->slug . '" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>
                        ' . ($record->is_approved_by_verifikator == true ? '
                        <a href="' . url('/sp3/' . $record->slug . '/preview') . '" class="btn btn-sm bg-success-light">
                            <i class="fa fa-print me-2"></i>
                        </a>' : '') . '
                    </div>
                </td>
            ';
            // dd($record);
            $data_arr[] = [
                "no_sp3"         => $record->no_surat_sp3 ?? '-',
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
                "tgl_berobat"     => $record->tgl_masuk && $record->tgl_keluar ? \Carbon\Carbon::parse($record->tgl_masuk)->translatedFormat('d F Y')
                    . ' - ' . \Carbon\Carbon::parse($record->tgl_keluar)->translatedFormat('d F Y') : null,
                "total_biaya"    => 'Rp ' . number_format($record->total_tagihan, 0, ',', '.'),
                "status"         => $status,
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
