@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Edit Billing</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('billing/list') }}">Billing</a></li>
                                <li class="breadcrumb-item active">Edit Billing</li>
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
                            <form action="{{ route('billing/update', $billing->slug) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="form-control" name="slug" value="{{ $billing->slug }}"
                                    readonly>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Keterangan<span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('keterangan') is-invalid @enderror"
                                                name="keterangan" value="{{ $billing->keterangan }}">
                                            @error('keterangan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>No Registrasi <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('no_registrasi') is-invalid @enderror"
                                                name="no_registrasi" value="{{ $billing->no_registrasi }}">
                                            @error('no_registrasi')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal Masuk <span class="login-danger">*</span></label>
                                            <input
                                                class="form-control datetimepicker @error('tanggal_masuk') is-invalid @enderror"
                                                name="tanggal_masuk" type="text" placeholder="DD-MM-YYYY"
                                                value="{{ old('tanggal_masuk') }}">
                                            @error('tanggal_masuk')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Tanggal Keluar <span class="login-danger">*</span></label>
                                            <input
                                                class="form-control datetimepicker @error('tanggal_keluar') is-invalid @enderror"
                                                name="tanggal_keluar" type="text" placeholder="DD-MM-YYYY"
                                                value="{{ old('tanggal_keluar') }}">
                                            @error('tanggal_keluar')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="student-submit">
                                            <button type="submit" class="btn btn-primary">Update</button>
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
@endsection
