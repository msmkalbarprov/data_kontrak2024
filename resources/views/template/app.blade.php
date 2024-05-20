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

    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" readonly disabled class="form-control" id="no_kontrak"
                                name="no_kontrak">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">ID Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" readonly disabled class="form-control" id="id_kontrak"
                                name="id_kontrak">
                            <input type="text" readonly disabled class="form-control" id="kd_skpd" name="kd_skpd"
                                hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Tanggal TTD</label>
                        <div class="col-md-10">
                            <input type="date" class="form-control" id="tanggal_ttd" name="tanggal_ttd">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">PPTK</label>
                        <div class="col-md-10">
                            <select name="pptk" class="form-control select_modal" id="pptk">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($dataTtd as $ttd)
                                    <option value="{{ $ttd->nip }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Margin --}}
                    <div class="mb-3 row">
                        <label for="sptb" class="col-md-12 col-form-label">
                            Ukuran Margin Untuk Cetakan PDF (Milimeter)
                        </label>
                        <label for="sptb" class="col-md-2 col-form-label"></label>
                        <label for="" class="col-md-1 col-form-label">Kiri</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_kiri" name="margin_kiri"
                                value="10">
                        </div>
                        <label for="" class="col-md-1 col-form-label">Kanan</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_kanan" name="margin_kanan"
                                value="10">
                        </div>
                        <label for="" class="col-md-1 col-form-label">Atas</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_atas" name="margin_atas"
                                value="10">
                        </div>
                        <label for="" class="col-md-1 col-form-label">Bawah</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_bawah" name="margin_bawah"
                                value="10">
                        </div>
                    </div>
                    {{-- Pengantar, Ringkasan dan Format Permandagri 77 --}}
                    <div class="mb-3 row">
                        <label for="pengantar" class="col-md-2 col-form-label">Pengantar</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md pengantar_layar" data-jenis="pdf"
                                name="pengantar_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pengantar_layar" data-jenis="layar"
                                name="pengantar_layar">Layar</button>
                        </div>
                        <label for="ringkasan" class="col-md-2 col-form-label">Ringkasan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md ringkasan"
                                data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md ringkasan"
                                data-jenis="layar">Layar</button>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end wrapper-->
    <!--start switcher-->
    @include('template.theme')
    <!--end switcher-->

    @include('template.js')
</body>


<!-- Mirrored from codervent.com/rocker/demo/horizontal/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 19 Mar 2024 06:15:00 GMT -->

</html>
