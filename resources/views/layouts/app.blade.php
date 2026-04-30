<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Login</title>
    <link rel="shortcut icon" href="{{ URL::to('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/fontawesome/css/all.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ URL::to('assets/css/style.css') }}">
    {{-- message toastr --}}
    <link rel="stylesheet" href="{{ URL::to('assets/css/toastr.min.css') }}">
    <script src="{{ URL::to('assets/js/toastr_jquery.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/toastr.min.js') }}"></script>
</head>

<body>
    <style>
        .invalid-feedback {
            font-size: 14px;
        }

        .min-height-100vh {
            min-height: 100vh;
        }
    </style>
    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container">
                <div class="loginbox">
                    <div class="login-left">
                        <div
                            class="col-md-6 d-flex align-items-center justify-content-center bg-info bg-gradient position-relative min-height-100vh">

                            <!-- Optional decorative circles -->
                            <div class="position-absolute top-0 start-0 translate-middle bg-light rounded-circle opacity-25"
                                style="width:150px; height:150px;"></div>
                            <div class="position-absolute bottom-0 end-0 translate-middle bg-light rounded-circle opacity-25"
                                style="width:120px; height:120px;"></div>

                            <!-- LOGO -->
                            <div class="text-center">
                                <img src="{{ URL::to('assets/img/Logo RSLNG.png') }}" class="img-fluid"
                                    style="max-width: 280px;">
                            </div>

                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/feather.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::to('assets/js/script.js') }}"></script>
</body>

</html>
