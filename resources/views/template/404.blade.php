<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from codervent.com/rocker/demo/horizontal/errors-404-error.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 19 Mar 2024 06:17:08 GMT -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('template/assets/images/favicon-32x32.png') }}" type="image/png" />
    <!-- loader-->
    <link href="{{ asset('template/assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('template/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('template/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/css/icons.css') }}" rel="stylesheet">
    <title>404 - NOT FOUND</title>
</head>

<body>
    <!-- wrapper -->
    <div class="wrapper">
        <div class="error-404 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="card py-5">
                    <div class="row g-0">
                        <div class="col col-xl-5">
                            <div class="card-body p-4">
                                <h1 class="display-1"><span class="text-primary">4</span><span
                                        class="text-danger">0</span><span class="text-success">4</span></h1>
                                <h2 class="font-weight-bold display-4">NOT FOUND</h2>
                                <p>Halaman tidak ada. <br>
                                    Silahkan ketik dengan benar.
                                </p>
                                <div class="mt-5"> <a href="{{ route('dashboard') }}"
                                        class="btn btn-primary btn-lg px-md-5 radius-30">Dashboard</a>
                                    <a href="{{ route('home') }}"
                                        class="btn btn-outline-dark btn-lg ms-3 px-md-5 radius-30">Kembali</a>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>

    </div>
    <!-- end wrapper -->
    <!-- Bootstrap JS -->
    <script src="{{ asset('template/assets/js/bootstrap.bundle.min.js') }}"></script>
</body>


<!-- Mirrored from codervent.com/rocker/demo/horizontal/errors-404-error.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 19 Mar 2024 06:17:09 GMT -->

</html>
