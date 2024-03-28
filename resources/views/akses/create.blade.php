@extends('template.app')
@section('konten')
    <div class="row">
        <div class="col-xl-10 mx-auto">
            @if (session('message'))
                <div class="alert alert-danger">
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h6 class="mb-0 text-uppercase">Tambah Akses</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <form method="POST"action="{{ route('akses.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="name" id="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-select @error('tipe') is-invalid @enderror" name="tipe" id="tipe">
                                <option value="1" {{ old('tipe') == '1' ? 'selected' : '' }}>Tanpa link</option>
                                <option value="2" {{ old('tipe') == '2' ? 'selected' : '' }}>Ada link</option>
                            </select>
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="input_link" hidden>
                            <label class="form-label">Link</label>
                            <input class="form-control @error('link') is-invalid @enderror" type="text"
                                placeholder="Isi dengan link" name="link" id="link" value="{{ old('link') }}">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Parent</label>
                            <select class="form-select @error('parent') is-invalid @enderror" name="parent" id="parent">
                                <option value="-" {{ old('parent') == '-' ? 'selected' : '' }}>Tidak ada</option>
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->uuid }}"
                                        {{ old('parent') == $permission->uuid ? 'selected' : '' }}>{{ $permission->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 text-end">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                            <a href="{{ route('akses.index') }}" class="btn btn-warning">Kembali</a>
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

            let tipe = "{{ old('tipe') }}";

            cekTipe(tipe)

            $('#tipe').on('change', function() {
                let tipe = $('#tipe').val();

                cekTipe(tipe)
            });

            function cekTipe(tipe) {
                if (tipe == '1') {
                    $('#input_link').prop('hidden', true);
                } else if (tipe == '2') {
                    $('#input_link').prop('hidden', false);
                }
            }
        });
    </script>
@endpush
