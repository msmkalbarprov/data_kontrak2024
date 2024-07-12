@extends('template.app')
@section('konten')
    <div class="row">
        <div class="col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body p-4">
                    @if (session('message'))
                        <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="font-35 text-white"><i class='bx bxs-check-circle'></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-white">Berhasil</h6>
                                    <div class="text-white">{{ session('message') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <h5 class="mb-4">UBAH SKPD</h5>
                    <form class="row g-3" action="{{ route('ubah_skpd.store') }}" method="POST">
                        @csrf
                        <div class="col-md-12">
                            <label for="kodeskpd" class="form-label">Satuan Kerja Perangkat Daerah (SKPD)</label>
                            <select id="kodeskpd" class="form-select select_option" name="kodeskpd">
                                <option selected="" disabled>Silahkan pilih...</option>
                                @foreach ($daftarSkpd as $item)
                                    <option value="{{ $item->kd_skpd }}"
                                        {{ $item->kd_skpd === Auth::user()->kd_skpd ? 'selected' : '' }}>
                                        {{ $item->kd_skpd }} - {{ $item->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="d-md-flex d-grid align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4">Simpan</button>
                            </div>
                        </div>
                    </form>
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
    </style>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endpush
