@extends('layouts.app')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    {{-- KANAN --}}
    <div class="login-page-right col-md-7 d-flex align-items-center justify-content-center p-4 p-md-5">
        <div class="w-100">
            <h2>Welcome To RS LNG Badak</h2>
            <h2>Sign in</h2>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Email<span class="login-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email">
                    <span class="profile-views"><i class="fas fa-envelope"></i></span>
                </div>
                <div class="form-group">
                    <label>Password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control pass-input @error('password') is-invalid @enderror"
                        name="password">
                    <span class="profile-views feather-eye toggle-password"></span>
                </div>
                <div class="forgotpass">
                    <a href="forgot-password.html">Forgot Password?</a>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-block" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
@endsection
