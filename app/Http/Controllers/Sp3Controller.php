<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sp3Request;
use App\Models\Billing;
use App\Models\Eslon;
use App\Models\Layanan;
use App\Models\PerihalTagihan;
use App\Models\Simrs\DepositKamarSimrs;
use App\Models\Simrs\EselonSimrs;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Simrs\TransaksiKamarSimrs;
use App\Models\Sp3;
use App\Service\Sp3Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Sp3Controller extends Controller
{
    private $kode_poli = [ //EXCLUDE RIN01 MCU01 dan LAB01
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
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        return view('sp3.index', compact('eselon'));
    }

    public function create()
    {
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->whereIn('nama', ['Rawat Jalan', 'Rawat Inap'])->get();
        return view('sp3.billing.create', compact('kode_tagihan', 'eselon', 'layanan'));
    }

    public function createSp3Deposit()
    {
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->get();
        // $deposit = DepositKamarSimrs::select(['no_reg', 'jumlah_deposit', 'updated_date', 'no_deposit', 'cara_bayar', 'keterangan'])->get();
        return view('sp3.deposito.create-sp3-deposito', compact('kode_tagihan', 'eselon', 'layanan'));
    }

    public function createSp3TagihanKeluar()
    {
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->get();
        return view('sp3.tagihan-keluar.create-sp3-tagihan-keluar', compact('kode_tagihan', 'eselon', 'layanan'));
    }

    public function createSp3Mcu()
    {
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->whereIn('nama', ['MCU', 'DCU', 'SKD', 'Pemeriksaan Narkoba'])->get();
        return view('sp3.mcu.create-sp3-mcu', compact('kode_tagihan', 'eselon', 'layanan'));
    }

    public function listAddDepositSp3($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        return view('sp3.deposito.adding-deposit-sp3', compact('sp3'));
    }

    public function listAddMcuSp3($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        return view('sp3.mcu.adding-mcu-sp3', compact('sp3'));
    }

    public function store(Sp3Request $request)
    {
        $validated = $request->validated();
        $sp3 = Sp3::where('eslon_id', $validated['eslon_id'])
            ->where('tgl_sp3', $validated['tgl_sp3'])
            ->where('tgl_masuk', $validated['tgl_masuk'])
            ->where('tgl_keluar', $validated['tgl_keluar'])
            ->where('layanan_id', $validated['layanan_id'])
            ->get();
        if ($sp3->count() > 0) {
            Toastr::error('Data SP3 dengan eselon dan tanggal yang sama sudah ada.', 'Error');
            return redirect()->back();
        }
        $tglMasuk  = Carbon::createFromFormat('d-m-Y', $validated['tgl_masuk'])->format('Y-m-d');
        $tglKeluar  = Carbon::createFromFormat('d-m-Y', $validated['tgl_keluar'])->format('Y-m-d');
        $eslon = Eslon::findOrFail($validated['eslon_id']);
        if ($validated['layanan_id'] == 1) { //jika layanan rawat inap
            // Ambil no_reg yang masih aktif (belum keluar dari RS)
            $noRegMasihAktif = TransaksiKamarSimrs::whereNull('tanggal_keluar')
                ->pluck('no_reg')
                ->values();

            $noRegSudahPulang = TransaksiKamarSimrs::whereRaw("DATE(tanggal_keluar) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
                ->whereNotIn('no_reg', $noRegMasihAktif) // exclude pasien yg masih aktif
                ->pluck('no_reg')
                ->values();

            $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
                ->with('masterPoli')
                ->where('eselon', $eslon->nama)
                ->whereIn('reg_no', $noRegSudahPulang)
                ->get()
                ->unique('reg_no');
        } else {
            $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
                ->with('masterPoli')
                ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
                ->whereIn('kode_poli', $this->kode_poli)
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('trans_kamar')
                        ->whereRaw('trans_kamar.no_reg = reg_multi_poli.reg_no');
                })
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
        }
        if ($getDataReg->isEmpty()) {
            Toastr::error('Data Billing Tidak Ada', 'Error');
            return redirect()->back();
        }
        $create = Sp3Service::createSp3Billing($validated, $getDataReg, $eslon);
        if ($create === true) {
            Toastr::success('Berhasil Menambahkan SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        }

        Toastr::error($create->getMessage(), 'Error');
        return redirect()->back();
    }

    public function storeSp3TagihanKeluar(Sp3Request $request)
    {
        $validated = $request->validated();
        $createSp3 = Sp3Service::createSp3($validated);
        if ($createSp3['status'] === 'success') {
            Toastr::success('Berhasil Menambahkan SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        } else {
            Toastr::error($createSp3['message'], 'Error');
            return redirect()->back();
        }
    }

    public function storeSp3Deposito(Sp3Request $request)
    {
        $validated = $request->validated();
        $createSp3 = Sp3Service::createSp3($validated);
        if ($createSp3['status'] === 'success') {
            $sp3 = $createSp3['data'];
            Toastr::success('Berhasil Menambahkan SP3 :)', 'Success');
            return redirect()->route('sp3/add/page/list-deposit', $sp3->slug);
        } else {
            Toastr::error($createSp3['message'], 'Error');
            return redirect()->back();
        }
    }

    public function storeSp3Mcu(Sp3Request $request)
    {
        $validated = $request->validated();
        $createSp3 = Sp3Service::createSp3($validated);
        if ($createSp3['status'] === 'success') {
            $sp3 = $createSp3['data'];
            Toastr::success('Berhasil Menambahkan SP3 :)', 'Success');
            return redirect()->route('sp3/add/page/list-mcu', $sp3->slug);
        } else {
            Toastr::error($createSp3['message'], 'Error');
            return redirect()->back();
        }
    }

    public function edit($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        $kode_tagihan = PerihalTagihan::select(['id', 'kode', 'hal'])->get();
        $eselon = Eslon::select(['id', 'nama', 'deskripsi'])->get();
        $layanan = Layanan::select(['id', 'nama'])->get();
        // dd($sp3);
        return view('sp3.billing.edit', compact('kode_tagihan', 'eselon', 'layanan', 'sp3'));
    }

    public function update(Sp3Request $request, $slug)
    {
        $validated = $request->validated();
        $tglMasuk  = Carbon::createFromFormat('d-m-Y', $validated['tgl_masuk'])->format('Y-m-d');
        $tglKeluar  = Carbon::createFromFormat('d-m-Y', $validated['tgl_keluar'])->format('Y-m-d');
        $eslon = Eslon::findOrFail($validated['eslon_id']);

        if ($validated['layanan_id'] == 1) { //jika layanan rawat inap
            // Ambil no_reg yang masih aktif (belum keluar dari RS)
            $noRegMasihAktif = TransaksiKamarSimrs::whereNull('tanggal_keluar')
                ->pluck('no_reg')
                ->values();

            $noRegSudahPulang = TransaksiKamarSimrs::whereRaw("DATE(tanggal_keluar) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
                ->whereNotIn('no_reg', $noRegMasihAktif) // exclude pasien yg masih aktif
                ->pluck('no_reg')
                ->values();

            $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
                ->with('masterPoli')
                ->where('eselon', $eslon->nama)
                ->whereIn('reg_no', $noRegSudahPulang)
                ->get()
                ->unique('reg_no');
        } else {
            $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
                ->with('masterPoli')
                ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
                ->whereIn('kode_poli', $this->kode_poli)
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('trans_kamar')
                        ->whereRaw('trans_kamar.no_reg = reg_multi_poli.reg_no');
                })
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
            $getDataReg = $getDataReg->unique('reg_no');
        }
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

    public function updateTagihanKeluar(Sp3Request $request, $slug)
    {
        $validated = $request->validated();
        $updateSp3 = Sp3Service::updateSp3TagihanKeluar($validated, $slug);
        if ($updateSp3['status'] === 'success') {
            Toastr::success('Berhasil Mengupdate SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        } else {
            Toastr::error($updateSp3['message'], 'Error');
            return redirect()->back();
        }
    }

    public function updateDeposito(Sp3Request $request, $slug)
    {
        $validated = $request->validated();
        $updateSp3 = Sp3Service::updateSp3Deposito($validated, $slug);
        if ($updateSp3['status'] === 'success') {
            Toastr::success('Berhasil Mengupdate SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        } else {
            Toastr::error($updateSp3['message'], 'Error');
            return redirect()->back();
        }
    }

    public function updateMcu(Sp3Request $request, $slug)
    {
        $validated = $request->validated();
        $updateSp3 = Sp3Service::updateSp3Deposito($validated, $slug);
        if ($updateSp3['status'] === 'success') {
            Toastr::success('Berhasil Mengupdate SP3 :)', 'Success');
            return redirect()->route('sp3-verifikasi/list');
        } else {
            Toastr::error($updateSp3['message'], 'Error');
            return redirect()->back();
        }
    }

    public function updateDataBilling($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        if ($sp3->layanan_id == 1) { //jika layanan rawat inap
            // Ambil no_reg yang masih aktif (belum keluar dari RS)
            $noRegMasihAktif = TransaksiKamarSimrs::whereNull('tanggal_keluar')
                ->pluck('no_reg')
                ->values();

            $noRegSudahPulang = TransaksiKamarSimrs::whereRaw("DATE(tanggal_keluar) BETWEEN ? AND ?", [$sp3->tgl_masuk, $sp3->tgl_keluar])
                ->whereNotIn('no_reg', $noRegMasihAktif) // exclude pasien yg masih aktif
                ->pluck('no_reg')
                ->values();

            $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
                ->with('masterPoli')
                ->where('eselon', $sp3->eselon->nama)
                ->whereIn('reg_no', $noRegSudahPulang)
                ->get()
                ->unique('reg_no');
        } else {
            $getDataReg = RegMultiPoliSimrs::select(['reg_no', 'nama', 'tanggal_registrasi', 'no_mr', 'kode_poli', 'eselon', 'jadi'])
                ->with('masterPoli')
                ->whereRaw("DATE(tanggal_registrasi) BETWEEN ? AND ?", [$sp3->tgl_masuk, $sp3->tgl_keluar])
                ->whereIn('kode_poli', $this->kode_poli)
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('trans_kamar')
                        ->whereRaw('trans_kamar.no_reg = reg_multi_poli.reg_no');
                })
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
        }
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
        $verified   = Billing::where('sp3_id', $sp3->id)->where('is_verified_by_verifikator', 1)->count();
        $unverified = Billing::where('sp3_id', $sp3->id)->where('is_verified_by_verifikator', 0)->count();
        return view('sp3.billing.detail-list-bill', compact('sp3', 'verified', 'unverified'));
    }

    public function approveSp3($slug)
    {
        $sp3 = Sp3::with('billings')->where('slug', $slug)->first();

        // Ambil SP3 terakhir di tahun yang sedang berjalan
        $latestSp3 = Sp3::select('no_sp3')
            ->whereNotNull('no_sp3')
            ->whereYear('created_at', now()->year)
            ->orderBy('no_sp3', 'desc')
            ->first();
        // Tentukan no_sp3 berikutnya
        if ($latestSp3) {
            // Masih tahun yang sama → lanjut +1
            $no_sp3 = $latestSp3->no_sp3 + 1;
        } else {
            // Tahun baru atau belum ada data → reset ke starter
            $no_sp3 = config('sp3.starter_number', 1);
        }
        $allVerified = $sp3->billings->every(fn($billing) => $billing->is_verified_by_verifikator == true);
        if (!$allVerified) {
            // Toastr::error('Billing Sp3 ini belum semua disetujui.', 'Error');
            // return redirect()->back();
            return response()->json([
                'success' => false,
                'message' => 'Billing SP3 ini belum semua disetujui.'
            ], 422);
        }
        $approveSp3 = Sp3Service::approveSp3($sp3, $no_sp3);
        if ($approveSp3['status'] === 'success') {
            return response()->json([
                'success' => true,
                'message' => $approveSp3['message']
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => $approveSp3['message']
        ], 500);
    }

    public function unapproveSp3($slug)
    {
        $sp3 = Sp3::with('billings')->where('slug', $slug)->first();
        $unapproveSp3 = Sp3Service::unapproveSp3($sp3);
        if ($unapproveSp3['status'] === 'success') {
            return response()->json([
                'success' => true,
                'message' => $unapproveSp3['message']
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => $unapproveSp3['message']
        ], 500);
    }

    public function previewSp3($slug)
    {
        $sp3 = Sp3::where('slug', $slug)->first();
        $cob = $sp3->billings->sum(fn($b) => $b->cob);
        $jenis_pembayaran = $sp3->ket_pembayaran == 'Pembayaran Biaya' ? 'Pembayaran' : 'Penagihan';
        if ($sp3->jenis_sp3 === 'billing' || $sp3->jenis_sp3 === 'mcu') {
            $deposit = $sp3->billings->sum(fn($b) => $b->deposit);
            $tagihan = $sp3->total_tagihan;
            $jumlah_pembayaran = $tagihan - $cob;
        } else if ($sp3->jenis_sp3 === 'deposito') {
            $tagihan = $sp3->billings->sum(fn($b) => $b->biaya_deposit);
            $deposit = 0;
            $jumlah_pembayaran = $tagihan - $cob;
        } else {
            $tagihan = $sp3->total_biaya ?? $sp3->total_tagihan;
            $deposit = 0;
            $jumlah_pembayaran = $sp3->total_biaya ?? $sp3->total_tagihan;
        }
        $data = [
            'jenis_sp3' => $sp3->jenis_sp3,
            'nomor' => $sp3->no_surat_sp3,
            'tanggal' => \Carbon\Carbon::parse($sp3->tgl_sp3)->translatedFormat('d F Y'),
            'pasien' => $sp3->eselon->deskripsi,
            'tagihan' => $tagihan,
            'cob' => $cob,
            'jumlah_pembayaran' => $jumlah_pembayaran,
            'total_kunjungan' => $sp3->kunjungan ?? $sp3->total_kunjungan,
            'total_pasien' => $sp3->pasien ?? $sp3->total_pasien,
            'hal' => $sp3->perihalTagihan->hal,
            'ket_pembayaran' => $sp3->ket_pembayaran,
            'layanan' => $sp3->layanan->nama,
            'nama_rs' => $sp3->nama_rs,
            'range_tgl' => $sp3->tgl_masuk && $sp3->tgl_keluar ? \Carbon\Carbon::parse($sp3->tgl_masuk)->translatedFormat('d F Y')
                . ' - ' . \Carbon\Carbon::parse($sp3->tgl_keluar)->translatedFormat('d F Y') : null,
            'eselon' => $sp3->eselon->deskripsi,
            'disetujui_oleh' => 'dr. RIEN POTU AGUSTINA',
            'diketahui_oleh' => 'dr. PUTU ISMA SARASWATI DEWI',
            'dibuat_oleh' => auth()->user()->rolename != 'Super Admin' ? auth()->user()->name : '',
            'ttd_path' => '',
            'keterangan' => $sp3->keterangan,
            'jenis_pembayaran' => $jenis_pembayaran
        ];
        // dd($data);
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

        $dari_tgl    = $request->get('dari_tgl')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('dari_tgl'))->startOfDay()
            : \Carbon\Carbon::now()->subDays(30)->startOfDay();

        $sampai_tgl  = $request->get('sampai_tgl')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('sampai_tgl'))->endOfDay()
            : \Carbon\Carbon::now()->endOfDay();

        $totalRecords = Sp3::count();

        $totalRecordsWithFilter = Sp3::where(function ($query) use ($searchValue) {
            $query->orWhere('no_surat_sp3', 'like', '%' . $searchValue . '%');
            $query->orWhere('nomor_tagihan', 'like', '%' . $searchValue . '%');
            $query->orWhere('ket_inv_pasien', 'like', '%' . $searchValue . '%');
            $query->orWhere('ket_inv_rs', 'like', '%' . $searchValue . '%');
            $query->orWhere('ket_pembayaran', 'like', '%' . $searchValue . '%');
            $query->orWhere('kota', 'like', '%' . $searchValue . '%');
            $query->orWhere('nama_rs', 'like', '%' . $searchValue . '%');
            $query->orWhere('dokter_rujukan', 'like', '%' . $searchValue . '%');
        })->count();

        $records = Sp3::with('eselon')
            // ->orderBy('is_approved_by_verifikator', 'ASC')
            // ->whereBetween('tgl_masuk', [$dari_tgl, $sampai_tgl])
            ->orderBy('created_at', 'DESC')
            ->orderBy('no_surat_sp3', 'DESC')
            ->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->orWhere('no_surat_sp3', 'like', '%' . $searchValue . '%');
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
            $status = $record->is_approved_by_verifikator ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-secondary">Belum Terverifikasi</span>';
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a href="' . url('sp3/detail/' . $record->slug) . '" class="btn btn-sm bg-success-light">
                            <i class="far fa-eye me-2"></i>
                        </a>
                        ' . ($record->is_approved_by_verifikator != true ? '
                        <a href="' . url('sp3/edit/' . $record->slug) . '" class="btn btn-sm bg-danger-light">
                            <i class="far fa-edit me-2"></i>
                        </a> 
                        <a class="btn btn-sm bg-danger-light delete slug" data-bs-toggle="modal" data-slug="' . $record->slug . '" data-bs-target="#delete">
                        <i class="fe fe-trash-2"></i>
                        </a>' : '') . ($record->is_approved_by_verifikator != true ? '
                        <a href="#" class="btn btn-sm bg-success-light btn-approve" data-url="' . url('/sp3/approve/' . $record->slug) . '">
                            <i class="fa fa-check me-2"></i>
                        </a>' : '<a href="#" 
                                data-url="' . url('/sp3/unapprove/' . $record->slug) . '" 
                                class="btn btn-sm bg-success-light btn-unapprove">
                                    <i class="fa fa-times me-2"></i>
                            </a>') . '
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
                "tgl_sp3"     => \Carbon\Carbon::parse($record->tgl_sp3)->translatedFormat('d M Y'),
                "nomor_tagihan"    => $record->nomor_tagihan,
                "tgl_terima_keu"    => \Carbon\Carbon::parse($record->tgl_terima_keu)->translatedFormat('d M Y'),
                "perihal_tagihan"    => $record->perihalTagihan->kode,
                "ket_inv_pasien"    => $record->ket_inv_pasien,
                "ket_inv_rs"    => $record->ket_inv_rs,
                "eselon"    => $record->eselon->nama,
                "jumlah_pasien"    => $record->pasien ?? $record->total_pasien,
                "jumlah_kunjungan"    => $record->kunjungan ?? $record->total_kunjungan,
                "ket_pembayaran"    => $record->ket_pembayaran,
                "layanan"    => $record->layanan->nama,
                "tgl_berobat"     => $record->tgl_masuk && $record->tgl_keluar ? \Carbon\Carbon::parse($record->tgl_masuk)->translatedFormat('d M Y')
                    . ' - ' . \Carbon\Carbon::parse($record->tgl_keluar)->translatedFormat('d M Y') : null,
                "total_tagihan"    => 'Rp ' . number_format($record->total_tagihan, 0, ',', '.'),
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

    /** get deposit data */
    public function getDepositData(Request $request)
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

        $totalRecords = DepositKamarSimrs::count();

        $totalRecordsWithFilter = DepositKamarSimrs::where(function ($query) use ($searchValue) {
            $query->where('no_reg', 'like', '%' . $searchValue . '%')
                ->orWhere('jumlah_deposit', 'like', '%' . $searchValue . '%')
                ->orWhere('update_date', 'like', '%' . $searchValue . '%')
                ->orWhere('no_deposit', 'like', '%' . $searchValue . '%')
                ->orWhere('cara_bayar', 'like', '%' . $searchValue . '%')
                ->orWhere('keterangan', 'like', '%' . $searchValue . '%')
                ->orWhereHas('registrasi', function ($q) use ($searchValue) {
                    $q->where('nama', 'like', '%' . $searchValue . '%');
                });
        })
            ->get()
            ->unique('no_reg')
            ->count();

        $records = DepositKamarSimrs::with('registrasi')
            ->orderByDesc('update_date')
            ->where(function ($query) use ($searchValue) {
                $query->where('no_reg', 'like', '%' . $searchValue . '%')
                    ->orWhere('jumlah_deposit', 'like', '%' . $searchValue . '%')
                    ->orWhere('update_date', 'like', '%' . $searchValue . '%')
                    ->orWhere('no_deposit', 'like', '%' . $searchValue . '%')
                    ->orWhere('cara_bayar', 'like', '%' . $searchValue . '%')
                    ->orWhere('keterangan', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('registrasi', function ($q) use ($searchValue) {
                        $q->where('nama', 'like', '%' . $searchValue . '%');
                    });
            })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get()
            ->unique('no_reg');
        $data_arr = [];
        $sp3Slug = $request->get('sp3_slug');
        foreach ($records as $key => $record) {
            $modify = '
                <td class="text-end"> 
                    <div class="actions">
                        <a data-no-reg="' . $record->no_reg . '" 
                        data-url="' . url('billing/' . $sp3Slug . '/' . $record->no_reg) . '" 
                        class="btn btn-sm bg-success-light btn-add-deposit">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </td>
            ';
            $deposit =  DepositKamarSimrs::where('no_reg', $record->no_reg)->get();
            $jumlah_deposit = (int) ceil($deposit->sum(fn($b) => $b->jumlah_deposit));
            $data_arr[] = [
                "no_reg"         => $record->no_reg,
                "nama"         => $record->registrasi->nama ?? '-',
                "jumlah_deposit"     => 'Rp ' . number_format($jumlah_deposit, 0, ',', '.'),
                "update_date"    => \Carbon\Carbon::parse($record->update_date)->translatedFormat('d M Y'),
                // "no_deposit"    => $record->no_deposit,
                // "cara_bayar"    => $record->cara_bayar,
                "keterangan"    => $record->keterangan,
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
