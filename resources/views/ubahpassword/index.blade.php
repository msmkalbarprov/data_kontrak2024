@extends('template.app')
@section('konten')
    <div class="row">
        <div class="col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-white">Error</h6>
                                    <div class="text-white">{{ session('error') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
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
                    <h5 class="mb-4">UBAH PASSWORD</h5>
                    <form class="row g-3" action="{{ route('ubah_password.store') }}" method="POST">
                        @csrf
                        <div class="col-md-12">
                            <label for="old_password" class="form-label">Password Lama</label>
                            <input type="password" class="form-control @error('old_password') is-invalid @enderror"
                                name="old_password" placeholder="Silahkan input password lama...">
                            @error('old_password')
                                <div style="color: red">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                name="new_password" placeholder="Silahkan input password baru...">
                            @error('new_password')
                                <div style="color: red">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="confirmation_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control @error('confirmation_password') is-invalid @enderror"
                                name="confirmation_password" placeholder="Silahkan input konfirmasi password...">
                            @error('confirmation_password')
                                <div style="color: red">{{ $message }}</div>
                            @enderror
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
