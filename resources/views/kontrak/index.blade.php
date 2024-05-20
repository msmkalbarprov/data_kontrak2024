@extends('template.app')
@section('konten')
    <div class="card radius-10">
        @if (session('message'))
            <div class="alert">{{ session('message') }}</div>
        @endif
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">KONTRAK</h6>
                </div>
                <div class="dropdown ms-auto">
                    <a href="{{ route('kontrak.create') }}" class="btn btn-success">Tambah</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="kontrak" style="width: 100%">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor <br>Kontrak</th>
                            <th>Tanggal <br>Kontrak</th>
                            <th>SKPD</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                        <label class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" readonly disabled class="form-control" id="no_kontrak" name="no_kontrak">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">ID Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" readonly disabled class="form-control" id="id_kontrak" name="id_kontrak">
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
                            <button type="button" class="btn btn-danger btn-md ringkasan" data-jenis="pdf">PDF</button>
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

            $('#kontrak').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kontrak.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                order: ['1', 'DESC'],
                pageLength: 10,
                searching: true,
                // ordering: false,
                columns: [{
                        data: 'nomorkontrak',
                        name: 'nomorkontrak'
                    }, {
                        data: 'tanggalkontrak',
                        name: 'tanggalkontrak'
                    }, {
                        data: 'namaskpd',
                        name: 'namaskpd'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi'
                    }
                ],
                columnDefs: [{
                    "className": "dt-center",
                    "targets": "_all"
                }]
            });
        });

        function hapus(id, nomorkontrak, kd_skpd) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success right-gap",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Apakah anda yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Tidak, kembali!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('kontrak.delete') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            nomorkontrak: nomorkontrak,
                            kd_skpd: kd_skpd
                        },
                        success: function(response) {
                            if (response.status == true) {
                                swalWithBootstrapButtons.fire({
                                    title: "Terhapus!",
                                    text: "Data berhasil dihapus!",
                                    icon: "success"
                                });
                                let tabel = $('#kontrak').DataTable();
                                tabel.ajax.reload();
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Gagal!",
                                    text: response.message,
                                    icon: "danger"
                                });
                            }
                        },
                        error: function(e) {
                            let errors = e.responseJSON;

                            Swal.fire({
                                title: "Error!",
                                text: errors.message,
                                icon: "error"
                            });
                        },
                    });

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Batal",
                        text: "Data tidak dihapus!",
                        icon: "error"
                    });
                }
            });
        }
    </script>
@endpush
