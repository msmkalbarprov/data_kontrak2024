@extends('template.app')
@section('konten')
    <div class="card radius-10">
        @if (session('message'))
            <div class="alert">{{ session('message') }}</div>
        @endif
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">LAPORAN KONTRAK</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-info collapsed-card card-outline" id="pengadaan_kontrak">
                        <div class="card-body">
                            {{ 'Pengadaan Kontrak' }}
                            <a class="card-block stretched-link" href="#">

                            </a>
                            <i class="fa fa-chevron-right float-end mt-2"></i>

                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-6">
                    <div class="card card-info collapsed-card card-outline" id="pembantu_penerimaan">
                        <div class="card-body">
                            {{ 'Buku Kas Pembantu Penerimaan' }}
                            <a class="card-block stretched-link" href="#">

                            </a>
                            <i class="fa fa-chevron-right float-end mt-2"></i>

                        </div>
                    </div>
                </div> --}}
            </div>

        </div>
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
                        <label class="col-md-2 col-form-label">PA/KPA</label>
                        <div class="col-md-10">
                            <select name="PA/KPA" class="form-control select_modal" id="pa_kpa">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($dataTtd as $ttd)
                                    <option value="{{ $ttd->nip }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">PPK</label>
                        <div class="col-md-10">
                            <select name="ppk" class="form-control select_modal" id="ppk">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($dataPpk as $ppk)
                                    <option value="{{ $ppk->nip }}">
                                        {{ $ppk->nip }} | {{ $ppk->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Tanggal TTD</label>
                        <div class="col-md-10">
                            <input type="date" class="form-control" id="tanggal_ttd" name="tanggal_ttd">
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
                            <input type="number" class="form-control" id="margin_kiri" name="margin_kiri" value="10">
                        </div>
                        <label for="" class="col-md-1 col-form-label">Kanan</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_kanan" name="margin_kanan" value="10">
                        </div>
                        <label for="" class="col-md-1 col-form-label">Atas</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_atas" name="margin_atas" value="10">
                        </div>
                        <label for="" class="col-md-1 col-form-label">Bawah</label>
                        <div class="col-md-1">
                            <input type="number" class="form-control" id="margin_bawah" name="margin_bawah" value="10">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md pengadaan" data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pengadaan" data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <style>
        .right-gap {
            margin-right: 10px
        }

        th.dt-center {
            text-align: center;
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#pengadaan_kontrak').on('click', function() {
                $('#modal_cetak').modal('show')
            });

            $(".pengadaan").on('click', function() {
                let pa_kpa = $('#pa_kpa').val();
                let ppk = $('#ppk').val();
                let tanggal_ttd = $('#tanggal_ttd').val();
                let margin_atas = $('#margin_atas').val();
                let margin_bawah = $('#margin_bawah').val();
                let margin_kanan = $('#margin_kanan').val();
                let margin_kiri = $('#margin_kiri').val();
                let jenis_print = $(this).data("jenis");

                if (!pa_kpa) {
                    swalAlert('Silahkan pilih PA/KPA');
                    return
                }

                if (!ppk) {
                    swalAlert('Silahkan pilih PPK');
                    return
                }

                if (!tanggal_ttd) {
                    swalAlert('Silahkan isi tanggal ttd');
                    return
                }

                let url = new URL("{{ route('laporan_kontrak.pengadaan') }}");
                let searchParams = url.searchParams;
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppk", ppk);
                searchParams.append("tanggal_ttd", tanggal_ttd);
                searchParams.append("margin_atas", margin_atas);
                searchParams.append("margin_bawah", margin_bawah);
                searchParams.append("margin_kanan", margin_kanan);
                searchParams.append("margin_kiri", margin_kiri);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            })
        });
    </script>
@endpush
