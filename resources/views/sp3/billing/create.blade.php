@extends('layouts.master')
@section('content')
    {{-- {!! Toastr::message() !!} --}}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Buat SP3</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('sp3/add/page') }}">SP3</a></li>
                                <li class="breadcrumb-item active">Buat SP3</li>
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
                            <form action="{{ route('sp3/add/save') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-title student-info">Form Tambah SP3
                                        </h5>
                                    </div>
                                    <input type="hidden" name="jenis_sp3" value="billing">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal SP3 <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_sp3') is-invalid @enderror"
                                                name="tgl_sp3" placeholder="DD-MM-YYYY" value="{{ old('tgl_sp3') }}">
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
                                                value="{{ old('jenis_surat', 'Pasien') }}">
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
                                                value="{{ old('nomor_tagihan') }}">
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
                                                value="{{ old('tgl_terima_keu') }}">
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
                                                        {{ old('perihal_tagihan_id') == $item->id ? 'selected' : '' }}>
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
                                                value="{{ old('ket_inv_pasien') }}">
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
                                                value="{{ old('ket_inv_rs', 'RS LNG Badak') }}">
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
                                                class="form-control select select2  @error('eslon_id') is-invalid @enderror"
                                                name="eslon_id">
                                                <option selected disabled>Select Eselon</option>
                                                @foreach ($eselon as $item)
                                                    <option value="{{ $item->id }}""
                                                        {{ old('eslon_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama . ' / ' . $item->deskripsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('eslon_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Keterangan Pembayaran <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('ket_pembayaran') is-invalid @enderror"
                                                name="ket_pembayaran" placeholder="Enter INV RS"
                                                value="{{ old('ket_pembayaran', 'Penagihan Biaya') }}">
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
                                                        {{ old('layanan_id') == $item->id ? 'selected' : '' }}>
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
                                                value="{{ old('nama_rs', 'RS LNG BADAK') }}">
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
                                                placeholder="Enter Kota" value="{{ old('kota', 'Bontang') }}">
                                            @error('kota')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Dari Tanggal <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_masuk') is-invalid @enderror"
                                                name="tgl_masuk" placeholder="DD-MM-YYYY"
                                                value="{{ old('tgl_masuk') }}">
                                            @error('tgl_masuk')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Sampai Tanggal <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datetimepicker @error('tgl_keluar') is-invalid @enderror"
                                                name="tgl_keluar" placeholder="DD-MM-YYYY"
                                                value="{{ old('tgl_keluar') }}">
                                            @error('tgl_keluar')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
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
@endsection
