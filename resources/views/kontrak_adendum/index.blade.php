@extends('template.app')
@section('konten')
    <div class="card radius-10">
        @if (session('message'))
            <div class="alert">{{ session('message') }}</div>
        @endif
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">KONTRAK ADENDUM</h6>
                </div>
                <div class="dropdown ms-auto">
                    <a href="{{ route('kontrak_adendum.create') }}" class="btn btn-success">Tambah</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="kontrak_adendum" style="width: 100%">
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

            $('#kontrak_adendum').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kontrak_adendum.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                    }
                },
                order: ['1', 'DESC'],
                pageLength: 10,
                searching: true,
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

        function hapus(id, nomorkontrak, nomorkontraklalu, kd_skpd) {
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
                        url: "{{ route('kontrak_adendum.delete') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            nomorkontrak: nomorkontrak,
                            nomorkontraklalu: nomorkontraklalu,
                            kd_skpd: kd_skpd
                        },
                        success: function(response) {
                            if (response.status == true) {
                                swalWithBootstrapButtons.fire({
                                    title: "Terhapus!",
                                    text: "Data berhasil dihapus!",
                                    icon: "success"
                                });
                                let tabel = $('#kontrak_adendum').DataTable();
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
