<!doctype html>
<html lang="en">


<!-- Mirrored from codervent.com/rocker/demo/horizontal/auth-cover-signup.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 19 Mar 2024 06:17:20 GMT -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('template/assets/images/favicon-32x32.png') }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('template/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('template/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('template/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('template/assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('template/assets/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('template/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('template/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/css/icons.css') }}" rel="stylesheet">
    <title>Data Kontrak</title>
</head>

<body class="">
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-cover">
            <div class="">
                <div class="row g-0">

                    <div
                        class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">

                        <div class="card shadow-none bg-transparent rounded-0 mb-0">
                            <div class="card-body">
                                <img src="{{ asset('template/assets/images/login-images/login-cover.svg') }}"
                                    class="img-fluid auth-img-cover-login" width="650" alt="" />
                            </div>
                        </div>

                    </div>

                    <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                        <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
                            <div class="card-body p-sm-5">
                                <div class="">
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('template/assets/images/logo-icon.png') }}" width="60"
                                            alt="">
                                    </div>
                                    <div class="text-center mb-4">
                                        <h5 class="">Data Kontrak</h5>
                                        <p class="mb-0">Silahkan isi <i>username</i> dan <i>password</i> Anda</p>
                                    </div>
                                    <div class="form-body">
                                        <form method="POST" class="row g-3" action="{{ route('login') }}">
                                            @csrf
                                            <div class="col-12">
                                                <label for="usernmae" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username"
                                                    name="username" placeholder="Silahkan isi username" autofocus>
                                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                                            </div>
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" class="form-control border-end-0"
                                                        id="password" placeholder="Silahkan isi password"
                                                        name="password"> <a href="javascript:;"
                                                        class="input-group-text bg-transparent"><i
                                                            class='bx bx-hide'></i></a>
                                                </div>
                                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">Login</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->
    <!-- Bootstrap JS -->
    <script src="{{ asset('template/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('template/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('template/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('template/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <!--Password show & hide js -->
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });
    </script>
    <!--app JS-->
    <script src="{{ asset('template/assets/js/app.js') }}"></script>
</body>


<!-- Mirrored from codervent.com/rocker/demo/horizontal/auth-cover-signup.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 19 Mar 2024 06:17:20 GMT -->

</html>
