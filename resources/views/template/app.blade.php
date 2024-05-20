<!doctype html>
<html lang="en">


@include('template.head')

<body>
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>
    <!--wrapper-->
    <div class="wrapper">
        <!--start header wrapper-->
        <div class="header-wrapper">
            <!--start header -->
            @include('template.header')
            <!--end header -->
            <!--navigation-->
            @include('template.menu')
            <!--end navigation-->
        </div>
        <!--end header wrapper-->
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                @yield('konten')
            </div>
        </div>
        <!--end page wrapper -->

        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i
                class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">Copyright © 2024. All right reserved.</p>
        </footer>
    </div>
    <!--end wrapper-->
    <!--start switcher-->
    @include('template.theme')
    <!--end switcher-->

    @include('template.js')
</body>


<!-- Mirrored from codervent.com/rocker/demo/horizontal/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 19 Mar 2024 06:15:00 GMT -->

</html>
