@extends('template.app')
@section('konten')
    <div class="row">
        <div class="col-xl-10 mx-auto">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h6 class="mb-0 text-uppercase">Tambah User</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <form method="POST"action="{{ route('user.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="name" id="name" value="{{ old('name') }}"
                                autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input class="form-control @error('username') is-invalid @enderror" type="text"
                                placeholder="Isi dengan username" name="username" id="username"
                                value="{{ old('username') }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group" id="show_hide_password">
                                <input type="password" class="form-control border-end-0" id="password"
                                    placeholder="Silahkan isi password" name="password"> <a href="javascript:;"
                                    class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group" id="show_hide_confirmation_password">
                                <input type="password" class="form-control border-end-0" id="confirmation_password"
                                    placeholder="Silahkan isi kembali password" name="confirmation_password"> <a
                                    href="javascript:;" class="input-group-text bg-transparent"><i
                                        class='bx bx-hide'></i></a>
                            </div>
                            @error('confirmation_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKPD</label>
                            <select class="form-select @error('kd_skpd') is-invalid @enderror select_option" name="kd_skpd"
                                id="kd_skpd" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($kd_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}"
                                        {{ old('kd_skpd') == $skpd->kd_skpd ? 'selected' : '' }}>{{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select @error('status_aktif') is-invalid @enderror select_option"
                                name="status_aktif" id="status_aktif" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                                <option value="1" {{ old('status_aktif') == '1' ? 'selected' : '' }}>
                                    Aktif</option>
                            </select>
                            @error('status_aktif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-select @error('tipe') is-invalid @enderror select_option" name="tipe"
                                id="tipe" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="admin" {{ old('tipe') == 'admin' ? 'selected' : '' }}>
                                    Admin</option>
                                <option value="skpd" {{ old('tipe') == 'skpd' ? 'selected' : '' }}>
                                    SKPD</option>
                            </select>
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peran</label>
                            <select class="form-select @error('role') is-invalid @enderror select_option" name="role"
                                id="role" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftar_peran as $peran)
                                    <option value="{{ $peran->uuid }}"
                                        {{ old('role') == $peran->uuid ? 'selected' : '' }}>
                                        {{ $peran->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input class="form-control @error('jabatan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="jabatan" id="jabatan" value="{{ old('jabatan') }}">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 text-end">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                            <a href="{{ route('user.index') }}" class="btn btn-warning">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

            $("#show_hide_confirmation_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_confirmation_password input').attr("type") == "text") {
                    $('#show_hide_confirmation_password input').attr('type', 'password');
                    $('#show_hide_confirmation_password i').addClass("bx-hide");
                    $('#show_hide_confirmation_password i').removeClass("bx-show");
                } else if ($('#show_hide_confirmation_password input').attr("type") == "password") {
                    $('#show_hide_confirmation_password input').attr('type', 'text');
                    $('#show_hide_confirmation_password i').removeClass("bx-hide");
                    $('#show_hide_confirmation_password i').addClass("bx-show");
                }
            });
        });
    </script>
@endpush
