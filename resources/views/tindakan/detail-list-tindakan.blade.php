@extends('layouts.master')
@section('content')
    {{-- {{ dd($billing) }} --}}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Detail Billing SP3 / {{ $billing->no_registrasi }}</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{ route('sp3/detail', $billing->sp3->slug) }}">Billing</a></li>
                                <li class="breadcrumb-item active">All Layanan Tindakan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row align-items-start mb-3">
                <div class="col">
                    <a href="{{ route('sp3/detail', $billing->sp3->slug) }}" type="button" class="btn btn-primary"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i> Kembali</a>
                </div>
            </div> --}}
            {{-- message --}}
            {{-- {!! Toastr::message() !!}
            <div class="student-group-form">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search by ID ...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search by Name ...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search by Phone ...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="search-student-btn">
                            <button type="btn" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <table class = "page-title">
                                            <tr class="mx-3">
                                                <td><b>No Registrasi</b></td>
                                                <td>: {{ $billing->no_registrasi }}</td>
                                            </tr>
                                            <tr class="mx-3">
                                                <td><b>Nama Pasien</b></td>
                                                <td>: {{ $billing->nama_pasien }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Tanggal Registrasi</b></td>
                                                <td>:
                                                    {{ \Carbon\Carbon::parse($billing->tanggal_masuk)->translatedFormat('d F Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Eselon</b></td>
                                                <td>: {{ $billing->eselon->deskripsi }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Total Biaya Eselon</b></td>
                                                <td>:
                                                    <b>{{ 'Rp ' . number_format($billing->total_tagihan ?? $billing->total_biaya_eselon, 0, ',', '.') }}</b>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    {{-- <div class="col">
                                        <h3 class="page-title">Detail Layanan</h3>
                                    </div> --}}


                                    {{-- <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('billing-verifikasi/list') }}"
                                            class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a>
                                        <a href="{{ route('billing/add/page') }}" class="btn btn-primary"><i
                                                class="fas fa-plus"></i></a>
                                    </div> --}}
                                </div>
                                <div class="col-md-12">
                                    @if (!$tindakan->isEmpty())
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class = "fw-bold">
                                                    Tindakan
                                                </h4>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($tindakan as $item)
                                                    <li class="list-group-item">
                                                        <div class="row justify-content-between">
                                                            <div class="col-8">
                                                                {{ $item->nama_tindakan }}
                                                            </div>
                                                            <div class="col-1">
                                                                {{ $item->jumlah }}
                                                            </div>
                                                            <div class="col-3">
                                                                {{ 'Rp ' . number_format($item->total_biaya, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between fw-bold fs-5">
                                                        <div class="col-9">
                                                            Total
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($tindakan->sum('total_biaya'), 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!$alkes->isEmpty())
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class = "fw-bold">
                                                    Alkes
                                                </h4>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($alkes as $item)
                                                    <li class="list-group-item">
                                                        <div class="row justify-content-between">
                                                            <div class="col-8">
                                                                {{ $item->nama_tindakan }}
                                                            </div>
                                                            <div class="col-1">
                                                                {{ $item->jumlah }}
                                                            </div>
                                                            <div class="col-3">
                                                                {{ 'Rp ' . number_format($item->total_biaya, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between fw-bold fs-5">
                                                        <div class="col-9">
                                                            Total
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($alkes->sum('total_biaya'), 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!$resepRawatJalan->isEmpty())
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class = "fw-bold">
                                                    Resep
                                                </h4>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($resepRawatJalan as $item)
                                                    <li class="list-group-item">
                                                        <div class="row justify-content-between">
                                                            <div class="col-8">
                                                                {{ $item->nama_tindakan }}
                                                            </div>
                                                            <div class="col-1">
                                                                {{ $item->jumlah }}
                                                            </div>
                                                            <div class="col-3">
                                                                {{ 'Rp ' . number_format($item->total_biaya, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between fw-bold fs-5">
                                                        <div class="col-9">
                                                            Total
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($resepRawatJalan->sum('total_biaya'), 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!$resepRawatInap->isEmpty())
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class = "fw-bold">
                                                    KIP
                                                </h4>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($resepRawatInap as $item)
                                                    <li class="list-group-item">
                                                        <div class="row justify-content-between">
                                                            <div class="col-8">
                                                                {{ $item->nama_tindakan }}
                                                            </div>
                                                            <div class="col-1">
                                                                {{ $item->jumlah }}
                                                            </div>
                                                            <div class="col-3">
                                                                {{ 'Rp ' . number_format($item->total_biaya, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between fw-bold fs-5">
                                                        <div class="col-9">
                                                            Total
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($resepRawatInap->sum('total_biaya'), 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!$kamar->isEmpty())
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class = "fw-bold">
                                                    Kamar
                                                </h4>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($kamar as $item)
                                                    <li class="list-group-item">
                                                        <div class="row justify-content-between">
                                                            <div class="col-8">
                                                                {{ $item->id_kamar }}
                                                            </div>
                                                            <div class="col-1">
                                                                {{ $item->jumlah }}
                                                            </div>
                                                            <div class="col-3">
                                                                {{ 'Rp ' . number_format($item->total_biaya, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between fw-bold fs-5">
                                                        <div class="col-9">
                                                            Total
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($kamar->sum('total_biaya'), 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!$dataBiayaAdm == null)
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class = "fw-bold">
                                                    Lain-lain
                                                </h4>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between">
                                                        <div class="col-8">
                                                            {{ $dataBiayaAdm['nama_tindakan'] }}
                                                        </div>
                                                        <div class="col-1">
                                                            {{ $dataBiayaAdm['jumlah'] }}
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($dataBiayaAdm['biaya'], 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <div class="row justify-content-between fw-bold fs-5">
                                                        <div class="col-9">
                                                            Total
                                                        </div>
                                                        <div class="col-3">
                                                            {{ 'Rp ' . number_format($dataBiayaAdm['biaya'], 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
