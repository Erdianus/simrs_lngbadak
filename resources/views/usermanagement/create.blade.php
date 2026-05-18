@extends('layouts.master')
@section('content')
    {{-- {!! Toastr::message() !!} --}}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Create User</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('sp3/add/page') }}">User</a></li>
                                <li class="breadcrumb-item active">Create User</li>
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
                            <form action="{{ route('user/store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Full Name <span class="login-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name">
                                </div>
                                <div class="form-group">
                                    <label>Email <span class="login-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email">
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>Role Name <span class="login-danger">*</span></label>
                                        <select class="form-control select select2" name="role_name" id="role_name">
                                            <option selected disabled>Role Type</option>
                                            @foreach ($role as $name)
                                                <option value="{{ $name->role_type }}"> {{ $name->role_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Password <span class="login-danger">*</span></label>
                                    <input type="password"
                                        class="form-control pass-input  @error('password') is-invalid @enderror"
                                        name="password">
                                    {{-- <span class="profile-views feather-eye toggle-password"></span> --}}
                                </div>
                                <div class="form-group">
                                    <label>Confirm password <span class="login-danger">*</span></label>
                                    <input type="password"
                                        class="form-control pass-confirm @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation">
                                    {{-- <span class="profile-views feather-eye reg-toggle-password"></span> --}}
                                </div>
                                <div class="col-12">
                                    <div class="student-submit">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script></script>
@endsection
