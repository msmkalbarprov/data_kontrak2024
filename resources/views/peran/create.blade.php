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
            <h6 class="mb-0 text-uppercase">Tambah Peran</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <form method="POST"action="{{ route('peran.store') }}">
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
                            <label class="form-label">Akses</label>
                        </div>
                        <div class="row mb-3">
                            @foreach ($akses_tipe1 as $tipe1)
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <ul class="list-group list-group-flush">
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-uppercase">{{ $tipe1->name }}</h6>
                                                    <span><input type="checkbox" class="check" name="check1[]"
                                                            value="{{ $tipe1->uuid }}"
                                                            @if (is_array(old('check1')) && in_array($tipe1->uuid, old('check1'))) checked @endif></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                @foreach ($akses_tipe2 as $tipe2)
                                                    @if ($tipe1->uuid == $tipe2->parent)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $tipe2->name }} <span><input type="checkbox" name="akses[]"
                                                                    class="checkTipe2" value="{{ $tipe2->uuid }}"
                                                                    data-parent="{{ $tipe2->parent }}" id="akses[]"
                                                                    @if (is_array(old('akses')) && in_array($tipe2->uuid, old('akses'))) checked @endif></span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @error('akses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 text-end">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                            <a href="{{ route('peran.index') }}" class="btn btn-warning">Kembali</a>
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

            // $('.check').on('click', function() {
            //     let checkTipe = this.value;
            //     let check = this.checked;

            //     const allboxes = Array.from(document.querySelectorAll('.checkTipe2'));

            //     const lala = allboxes.map((item) => {
            //         if (item.dataset.parent == checkTipe && check) {
            //             item.checked = true
            //         } else if (!check && item.dataset.parent == checkTipe) {
            //             item.checked = false
            //         }
            //     })
            // });

        });
    </script>
@endpush
