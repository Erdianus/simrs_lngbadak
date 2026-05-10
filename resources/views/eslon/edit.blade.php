@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Edit Eselon</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('eselon/list') }}">Eselon</a></li>
                                <li class="breadcrumb-item active">Edit Eselon</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card comman-shadow">
                        <div class="card-body">
                            <form action="{{ route('eselon/update', $eselon->slug) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="form-control" name="slug" value="{{ $eselon->slug }}"
                                    readonly>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Nama <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                name="nama" value="{{ $eselon->nama }}">
                                            @error('nama')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Deskripsi <span class="login-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('deskripsi') is-invalid @enderror"
                                                name="deskripsi" value="{{ $eselon->deskripsi }}">
                                            @error('deskripsi')
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
