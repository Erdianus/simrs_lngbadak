@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Edit SP3</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('sp3/add/page') }}">SP3</a></li>
                                <li class="breadcrumb-item active">Edit SP3</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card comman-shadow">
                        <div class="card-body">
                            @php
                                $action = match ($sp3->jenis_sp3) {
                                    'billing' => route('sp3/update', $sp3->slug),
                                    'tagihan keluar' => route('sp3/update/tagihan-keluar', $sp3->slug),
                                    'deposito' => route('sp3/update/deposito', $sp3->slug),
                                    'mcu' => route('sp3/update/mcu', $sp3->slug),
                                };
                            @endphp
                            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-title student-info">Form Edit SP3 {{ ucwords($sp3->jenis_sp3) }}
                                        </h5>
                                    </div>
                                    <input type="hidden" name="jenis_sp3" value="{{ $sp3->jenis_sp3 }}">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal SP3 <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_sp3') is-invalid @enderror"
                                                name="tgl_sp3"
                                                value="{{ $sp3->tgl_sp3 ? \Carbon\Carbon::parse($sp3->tgl_sp3)->format('d-m-Y') : '' }}">
                                            @error('tgl_sp3')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Jenis Surat <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('jenis_surat') is-invalid @enderror"
                                                name="jenis_surat" placeholder="Enter Nama Eselon"
                                                value="{{ $sp3->jenis_surat }}">
                                            @error('jenis_surat')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>No Tagihan (INV RS/ER Klaim) <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nomor_tagihan') is-invalid @enderror"
                                                name="nomor_tagihan" placeholder="Enter No Tagihan"
                                                value="{{ $sp3->nomor_tagihan }}">
                                            @error('nomor_tagihan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal Terima Keuangan <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_terima_keu') is-invalid @enderror"
                                                name="tgl_terima_keu" placeholder="DD-MM-YYYY"
                                                value="{{ $sp3->tgl_terima_keu ? \Carbon\Carbon::parse($sp3->tgl_terima_keu)->format('d-m-Y') : '' }}">
                                            @error('tgl_terima_keu')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Kode Tagihan <span class="login-danger">*</span></label>
                                            <select
                                                class="form-control select select2 @error('perihal_tagihan_id') is-invalid @enderror"
                                                name="perihal_tagihan_id">
                                                <option selected disabled>Select Kode Tagihan</option>
                                                @foreach ($kode_tagihan as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $sp3->perihal_tagihan_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->kode . ' / ' . $item->hal }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('perihal_tagihan_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>INV RS : Nama RS, ER Klaim : Nama Pasien <span
                                                    class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('ket_inv_pasien') is-invalid @enderror"
                                                name="ket_inv_pasien" placeholder="Enter Keterangan INV Pasien"
                                                value="{{ $sp3->ket_inv_pasien }}">
                                            @error('ket_inv_pasien')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>INV RS:Nama Rekening Bank, ER Klaim:Nama RS <span
                                                    class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('ket_inv_rs') is-invalid @enderror"
                                                name="ket_inv_rs" placeholder="Enter INV RS"
                                                value="{{ $sp3->ket_inv_rs }}">
                                            @error('ket_inv_rs')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Eselon <span class="login-danger">*</span></label>
                                            <select
                                                class="form-control select select2 @error('eslon_id') is-invalid @enderror"
                                                name="eslon_id">
                                                <option selected disabled>Select Kode Tagihan</option>
                                                @foreach ($eselon as $item)
                                                    <option value="{{ $item->id }}""
                                                        {{ $sp3->eslon_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama . ' / ' . $item->deskripsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('eslon_id')
                                                <span class=" invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @if ($sp3->jenis_sp3 === 'tagihan keluar')
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group local-forms">
                                                <label>Kunjungan <span class="login-danger">*</span></label>
                                                <input type="number" name="kunjungan" id="kunjungan"
                                                    class="form-control"
                                                    value="{{ $sp3->kunjungan ?? old('kunjungan', 0) }}">
                                                @error('kunjungan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group local-forms">
                                                <label>Pasien <span class="login-danger">*</span></label>
                                                <input type="number" name="pasien" id="pasien" class="form-control"
                                                    value="{{ $sp3->pasien ?? old('pasien', 0) }}">
                                                @error('pasien')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Keterangan Pembayaran <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('ket_pembayaran') is-invalid @enderror"
                                                name="ket_pembayaran" placeholder="Enter INV RS"
                                                value="{{ $sp3->ket_pembayaran }}">
                                            @error('ket_pembayaran')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            @error('ket_pembayaran')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Layanan <span class="login-danger">*</span></label>
                                            <select
                                                class="form-control select select2 @error('layanan_id') is-invalid @enderror"
                                                name="layanan_id">
                                                <option selected disabled>Select Layanan</option>
                                                @foreach ($layanan as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $sp3->layanan_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('layanan_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Nama RS / Klinik Dokter <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nama_rs') is-invalid @enderror"
                                                name="nama_rs" placeholder="Enter Nama RS/Klinik Dokter"
                                                value="{{ $sp3->nama_rs }}">
                                            @error('nama_rs')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Kota <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('kota') is-invalid @enderror" name="kota"
                                                placeholder="Enter Kota" value="{{ $sp3->kota }}">
                                            @error('kota')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal Masuk <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_masuk') is-invalid @enderror"
                                                name="tgl_masuk" placeholder="DD-MM-YYYY"
                                                value="{{ $sp3->tgl_masuk ? \Carbon\Carbon::parse($sp3->tgl_masuk)->format('d-m-Y') : '' }}">
                                            @error('tgl_masuk')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal Keluar <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_keluar') is-invalid @enderror"
                                                name="tgl_keluar" placeholder="DD-MM-YYYY"
                                                value="{{ $sp3->tgl_keluar ? \Carbon\Carbon::parse($sp3->tgl_keluar)->format('d-m-Y') : '' }}">
                                            @error('tgl_keluar')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @if ($sp3->jenis_sp3 === 'tagihan keluar')
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group local-forms">
                                                <label>Total Tagihan <span class="login-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('total_tagihan') is-invalid @enderror"
                                                    name="total_tagihan_display" id="total_tagihan_display"
                                                    placeholder="Rp 0"
                                                    value="{{ $sp3->total_tagihan ? number_format($sp3->total_tagihan, 0, ',', '.') : '' }}"
                                                    autocomplete="off">

                                                {{-- Hidden input yang dikirim sebagai integer --}}
                                                <input type="hidden" name="total_tagihan" id="total_tagihan"
                                                    value="{{ $sp3->total_tagihan }}">

                                                @error('total_tagihan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    {{ $sp3->kete }}
                                    <div class="col-12 mb-3">
                                        <div class="form-group local-forms">
                                            <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="5">{{ $sp3->keterangan }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="student-submit">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        const display = document.getElementById('total_tagihan_display');
        const hidden = document.getElementById('total_tagihan');

        display.addEventListener('input', function() {
            // Hapus semua karakter selain angka
            let raw = this.value.replace(/\D/g, '');

            // Simpan nilai integer ke hidden input
            hidden.value = raw;

            // Format tampilan dengan titik sebagai pemisah ribuan
            this.value = raw ? 'Rp ' + parseInt(raw).toLocaleString('id-ID') : '';
        });
    </script>
@endsection
