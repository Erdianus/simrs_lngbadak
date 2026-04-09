
@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Edit Sub Layanan</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('sub-layanan/list') }}">Sub Layanan</a></li>
                                <li class="breadcrumb-item active">Edit Sub Layanan</li>
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
                            <form action="{{ route('sub-layanan/update',$subLayanan->slug) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="form-control" name="slug" value="{{ $subLayanan->slug }}" readonly>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Nama <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ $subLayanan->nama }}">
                                            @error('nama')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Layanan <span class="login-danger">*</span></label>
                                            <select class="form-control select @error('layanan_id') is-invalid @enderror" name="layanan_id">
                                                <option value="">Select Layanan</option>
                                                @foreach($layanans as $layanan)
                                                    <option value="{{ $layanan->id }}" {{ $subLayanan->layanan_id == $layanan->id ? 'selected' : '' }}>
                                                        {{ $layanan->nama }}
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
