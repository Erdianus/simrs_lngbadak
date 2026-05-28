<?php

namespace App\Service;

use App\Models\Billing;
use App\Models\Sp3;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Sp3Service
{

    public static function getSp3VerifikasiData($request)
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
                        <a href="' . url('sp3-verifikasi/detail/' . $record->slug) . '" class="btn btn-sm bg-success-light">
                            <i class="far fa-eye me-2"></i>
                        </a>
                        ' . ($record->is_approved_by_verifikator != true ? '
                        <a href="' . url('sp3-verifikasi/edit/' . $record->slug) . '" class="btn btn-sm bg-danger-light">
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
                "tgl_terima_keu"    => $record->tgl_terima_keu ? \Carbon\Carbon::parse($record->tgl_terima_keu)->translatedFormat('d M Y') : '-',
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
        return $response;
    }

    public static function getSp3KeuData($request)
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

        $totalRecords = Sp3::where('is_approved_by_verifikator', true)->count();

        $totalRecordsWithFilter = Sp3::where('is_approved_by_verifikator', true)
            ->where(function ($query) use ($searchValue) {
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
            ->where('is_approved_by_verifikator', true)
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
            $status = $record->tgl_terima_keu
                ? '<span class="badge bg-success">Sudah Diterima</span>'
                : '<span class="badge bg-secondary">Belum Diterima</span>';

            $modify = '
        <td class="text-end">
            <div class="actions">
                <a href="' . url('sp3-keuangan/detail/' . $record->slug) . '" class="btn btn-sm bg-success-light">
                    <i class="far fa-eye me-2"></i>
                </a>
                <a href="' . url('/sp3/' . $record->slug . '/preview') . '" class="btn btn-sm bg-success-light">
                    <i class="fa fa-print me-2"></i>
                </a>
                <a href="#" class="btn btn-sm bg-success-light revisi"
                    data-bs-toggle="modal"
                    data-slug="' . $record->slug . '"
                    data-bs-target="#revisi">
                    <i class="fa fa-edit"></i>
                </a>';

            $modify .= !$record->tgl_terima_keu ? '
                <a href="#" class="btn btn-sm bg-success-light btn-approve"
                    data-url="' . url('/sp3/receive/' . $record->slug) . '">
                    <i class="fa fa-check me-2"></i>
                </a>' : '';

            $modify .= '
            </div>
        </td>
    ';

            $data_arr[] = [
                "no_sp3"           => $record->no_surat_sp3 ?? '-',
                "tgl_sp3"          => \Carbon\Carbon::parse($record->tgl_sp3)->translatedFormat('d M Y'),
                "nomor_tagihan"    => $record->nomor_tagihan,
                "tgl_terima_keu"    => $record->tgl_terima_keu ? \Carbon\Carbon::parse($record->tgl_terima_keu)->translatedFormat('d M Y') : '-',
                "perihal_tagihan"  => $record->perihalTagihan->kode,
                "ket_inv_pasien"   => $record->ket_inv_pasien,
                "ket_inv_rs"       => $record->ket_inv_rs,
                "eselon"           => $record->eselon->nama,
                "jumlah_pasien"    => $record->pasien ?? $record->total_pasien,
                "jumlah_kunjungan" => $record->kunjungan ?? $record->total_kunjungan,
                "ket_pembayaran"   => $record->ket_pembayaran,
                "layanan"          => $record->layanan->nama,
                "tgl_berobat"      => $record->tgl_masuk && $record->tgl_keluar
                    ? \Carbon\Carbon::parse($record->tgl_masuk)->translatedFormat('d M Y')
                    . ' - '
                    . \Carbon\Carbon::parse($record->tgl_keluar)->translatedFormat('d M Y')
                    : null,
                "total_tagihan"    => 'Rp ' . number_format($record->total_tagihan, 0, ',', '.'),
                "status"           => $status,
                "modify"           => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"               => $data_arr
        ];
        return $response;
    }

    public static function createSp3Billing($data, $getDataReg, $eselon)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::create([
                'jenis_sp3' => $data['jenis_sp3'],
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                // 'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'keterangan' => $data['keterangan'],
            ]);
            // dd($sp3->computeTotalTagihan());
            $newBill = $getDataReg->pluck('no_registrasi')->toArray();
            $existingBill = Billing::where('eslon_id', $sp3->eslon_id)
                ->where('tanggal_keluar', '>=', $sp3->tgl_masuk)
                ->where('tanggal_keluar', '<=', $sp3->tgl_keluar)
                ->whereIn('no_registrasi', $newBill)
                ->get();
            if ($existingBill->count() > 0) {
                Toastr::error('Data Billing dengan eselon dan tanggal yang sama sudah ada.', 'Error');
                return redirect()->back();
            }
            BillingService::createBilling($getDataReg, $sp3, $eselon);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public static function createSp3($data)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::create([
                'jenis_sp3' => $data['jenis_sp3'],
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                // 'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('New SP3 created: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success',
                'data' => $sp3
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function updateSp3($data, $getDataReg, $eselon, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                // 'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'is_approved_by_verifikator' => false,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('Existing billings deleted for SP3 ID: ' . $sp3->billings); // ← tambahkan
            if ($sp3->billings()->count() > 0) {
                BillingService::deleteBilling($sp3->id);
            }
            BillingService::createBilling($getDataReg, $sp3, $eselon);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public static function updateSp3TagihanKeluar($data, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                // 'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('SP3 updated: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Berhasil update SP3 Tagihan Keluar'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function updateSp3Deposito($data, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                // 'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('SP3 updated: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Berhasil Mengupdate Sp3'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function updateSp3Mcu($data, $slug)
    {
        DB::beginTransaction();
        try {
            $sp3 = Sp3::where('slug', $slug)->first();
            $sp3->update([
                'jenis_sp3' => $data['jenis_sp3'],
                'tgl_sp3' => $data['tgl_sp3'],
                'jenis_surat' => $data['jenis_surat'],
                'nomor_tagihan' => $data['nomor_tagihan'],
                // 'tgl_terima_keu' => $data['tgl_terima_keu'],
                'perihal_tagihan_id' => $data['perihal_tagihan_id'],
                'ket_inv_pasien' => $data['ket_inv_pasien'],
                'ket_inv_rs' => $data['ket_inv_rs'],
                'eslon_id' => $data['eslon_id'],
                'ket_pembayaran' => $data['ket_pembayaran'],
                'layanan_id' => $data['layanan_id'],
                'kota' => $data['kota'],
                'nama_rs' => $data['nama_rs'],
                'dokter_rujukan' => $data['dokter_rujukan'] ?? null,
                'kunjungan' => $data['kunjungan'] ?? null,
                'pasien' => $data['pasien'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'],
                'tgl_keluar' => $data['tgl_keluar'],
                'total_tagihan' => $data['total_tagihan'] ?? 0,
                'keterangan' => $data['keterangan'],
            ]);
            Log::info('SP3 updated: ' . $sp3->id);
            DB::commit();
            return [
                'status' => 'success'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function refreshBillSp3($sp3, $getDataReg)
    {
        DB::beginTransaction();
        try {
            if ($sp3->billings()->count() > 0) {
                BillingService::deleteBilling($sp3->id);
            }
            BillingService::createBilling($getDataReg, $sp3, $sp3->eselon);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public static function deleteSp3($slug)
    {
        // Logika untuk menghapus data Eslon ke database
        DB::beginTransaction();
        try {
            $value = Sp3::where('slug', $slug)->first();
            $value->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function approveSp3($sp3, $no_sp3)
    {
        DB::beginTransaction();
        try {
            $no_surat_sp3 = str_pad($no_sp3, 4, '0', STR_PAD_LEFT) . '-V/RSBDK1100/' . now()->year . '-S2';
            if ($sp3->no_surat_sp3) {
                $sp3->update([
                    'is_approved_by_verifikator' => true
                ]);
            } else {
                $sp3->update([
                    'no_sp3' => $no_sp3,
                    'no_surat_sp3' => $no_surat_sp3,
                    'is_approved_by_verifikator' => true
                ]);
            }
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Billing berhasil disetujui'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];
        }
    }

    public static function unapproveSp3($sp3)
    {
        DB::beginTransaction();
        try {
            $sp3->update([
                'is_approved_by_verifikator' => false
            ]);
            Billing::where('sp3_id', $sp3->id)->update([
                'approved_verif_pic_by' => null,
                'is_verified_by_verifikator' => false
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Persetujuan Billing Berhasil dibatalkan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];;
        }
    }

    public static function receiveSp3($sp3)
    {
        DB::beginTransaction();
        try {
            $sp3->update([
                'tgl_terima_keu' => now()->format('Y-m-d'),
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Sp3 Berhasil diterima keuangan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];;
        }
    }

    public static function revisiSp3($sp3, $request)
    {
        $validated = $request->validate([
            'alasan_rev' => 'required|string',
        ]);
        if (!$validated) {
            return [
                'status' => 'failed',
                'message' => 'Alasan revisi harus diisi'
            ];
        }

        DB::beginTransaction();
        try {
            $sp3->update([
                'revisi' => $sp3->revisi + 1,
                'alasan_rev' => $validated['alasan_rev'],
                'tgl_terima_keu' => null,
                'is_approved_by_verifikator' => false
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Sp3 Berhasil diterima keuangan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status' => 'failed',
                'message' => $th->getMessage()
            ];;
        }
    }
}
