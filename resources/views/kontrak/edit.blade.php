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
            <h6 class="mb-0 text-uppercase">Edit Kontrak</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Id Kontrak</label>
                        <input class="form-control" type="text" readonly disabled id="id_kontrak"
                            value="{{ $kontrak->idkontrak }}">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">No. Kontrak</label>
                            <input class="form-control" type="text" id="no_kontrak"
                                placeholder="Isi dengan nomor kontrak" autofocus value="{{ $kontrak->nomorkontrak }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Kontrak</label>
                            <input class="form-control" type="date" id="tgl_kontrak"
                                value="{{ $kontrak->tanggalkontrak }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Kode SKPD/UNIT</label>
                            <input class="form-control" type="text" readonly disabled id="kd_skpd"
                                value="{{ $kontrak->kodeskpd }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Nama SKPD/UNIT</label>
                            <input class="form-control" type="text" readonly disabled id="nm_skpd"
                                value="{{ $kontrak->namaskpd }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Nama Pekerjaan</label>
                            <textarea class="form-control" id="nm_kerja" placeholder="Isi dengan nama pekerjaan">{{ $kontrak->pekerjaan }}</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">(Rekanan) Nama Pemilik Rekening</label>
                            <select class="form-select select_option" id="rekanan">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->nmrekan }}" data-rekening="{{ $rekening->rekening }}"
                                        data-bank="{{ $rekening->bank }}" data-nm_bank="{{ $rekening->nm_bank }}"
                                        data-npwp="{{ $rekening->npwp }}"
                                        {{ $rekening->nmrekan == $kontrak->rekanan ? 'selected' : '' }}>
                                        {{ $rekening->nmrekan }} |
                                        {{ $rekening->rekening }} | {{ $rekening->nm_bank }} | {{ $rekening->npwp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">No. Rekening</label>
                            <input class="form-control" type="text" id="no_rekening" readonly disabled
                                value="{{ $kontrak->rekening }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">NPWP</label>
                            <input class="form-control" type="text" id="npwp" readonly disabled
                                value="{{ $kontrak->npwp }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Pimpinan</label>
                            <input class="form-control" type="text" id="pimpinan" placeholder="Isi dengan nama pimpinan"
                                value="{{ $kontrak->pimpinan }}">
                        </div>
                        <div class="col-1">
                            <label class="form-label">Bank</label>
                            <input class="form-control" type="text" id="bank" readonly disabled
                                value="{{ $kontrak->bank }}">
                        </div>
                        <div class="col-5">
                            <label class="form-label">Nama Bank</label>
                            <input class="form-control" type="text" id="nm_bank" readonly disabled>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        @if ($cekKontrakAdendum == 0)
                            <button class="btn btn-primary" id="simpan">Simpan</button>
                        @endif
                        <a href="{{ route('kontrak.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Rincian Kontrak
                    <button class="btn btn-success btn-md float-end" id="tambah_rincian">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table class="table align-middle mb-0" id="rincian_kontrak" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Kode Sub Kegiatan</th>
                                <th>Kode Rekening</th>
                                <th>Kode Barang</th>
                                <th>Sumber Dana</th>
                                <th>Volume</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($detail_kontrak as $detail)
                                @php
                                    $cek = [$detail->volume1, $detail->volume2, $detail->volume3, $detail->volume4];
                                    $volume = array_reduce(
                                        $cek,
                                        function ($prev, $current) {
                                            if ($current != 0) {
                                                $prev *= $current;
                                            }
                                            return $prev;
                                        },
                                        1,
                                    );
                                    $total += $detail->nilai;
                                @endphp
                                <tr>

                                    <td>{{ $detail->idtrdpo }}</td>
                                    <td>{{ $detail->kodesubkegiatan }}</td>
                                    <td>{{ $detail->kodeakun }}</td>
                                    <td>{{ $detail->kodebarang }}</td>
                                    <td>{{ $detail->kodesumberdana }}</td>
                                    <td>{{ rupiah($volume) }}</td>
                                    <td>{{ rupiah($detail->harga) }}</td>
                                    <td>{{ rupiah($detail->nilai) }}</td>
                                    <td>
                                        @if ($cekKontrakAdendum == 0)
                                            <a href="javascript:void(0);"
                                                onclick="hapusRincian('{{ $detail->idtrdpo }}','{{ $detail->nilai }}')"
                                                class="btn btn-danger btn-sm"><i
                                                    class="fadeIn animated bx bx-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label class="col-md-8 col-form-label kanan">Total
                            Rincian Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control kanan" id="total_rincian_kontrak"
                                style="background-color:white;border:none" value="{{ rupiah($total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_rincian">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rincian Kontrak</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Kode Sub Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal" id="kd_sub_kegiatan"
                                data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Kode Rekening</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal" id="kd_rek6" data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Kode Barang</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal" id="kd_barang" data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Sumber Dana</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal" id="sumber" data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Volume 1</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume1">
                        </div>
                        <label class="form-label col-md-2">Satuan 1</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan1">
                        </div>
                        <label class="form-label col-md-2">Input Volume 1</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume1"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Volume 2</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume2">
                        </div>
                        <label class="form-label col-md-2">Satuan 2</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan2">
                        </div>
                        <label class="form-label col-md-2">Input Volume 2</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume2"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Volume 3</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume3">
                        </div>
                        <label class="form-label col-md-2">Satuan 3</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan3">
                        </div>
                        <label class="form-label col-md-2">Input Volume 3</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume3"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Volume 4</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume4">
                        </div>
                        <label class="form-label col-md-2">Satuan 4</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan4">
                        </div>
                        <label class="form-label col-md-2">Input Volume 4</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume4"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Total Volume</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume">
                        </div>
                        <label class="form-label col-md-2">Harga</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="harga">
                        </div>
                        <label class="form-label col-md-2">Total Anggaran</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="total">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-9 col-form-label kanan">Total
                            Rincian Kontrak</label>
                        <div class="col-md-3">
                            <input type="text" width="100%" class="form-control kanan" readonly
                                id="total_detail_kontrak" style="background-color:white;border:none"
                                value="{{ rupiah($total) }}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            @if ($cekKontrakAdendum == 0)
                                <button type="button" class="btn btn-success" id="simpan_rincian">Simpan</button>
                            @endif
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Kembali</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Rincian Kontrak
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table align-middle mb-0" id="detail_kontrak" style="width: 100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Kode Sub Kegiatan</th>
                                        <th>Nama Sub Kegiatan</th>
                                        <th>Kode Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Kode Barang</th>
                                        <th>Uraian</th>
                                        <th>Kode Sumber</th>
                                        <th>Sumber</th>
                                        <th>Spesifikasi</th>
                                        <th>Volume 1</th>
                                        <th>Volume 2</th>
                                        <th>Volume 3</th>
                                        <th>Volume 4</th>
                                        <th>Satuan 1</th>
                                        <th>Satuan 2</th>
                                        <th>Satuan 3</th>
                                        <th>Satuan 4</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>No PO</th>
                                        <th>Header</th>
                                        <th>Sub Header</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_detail = 0;
                                    @endphp
                                    @foreach ($detail_kontrak as $detail)
                                        @php
                                            $total_detail += $detail->nilai;
                                        @endphp
                                        <tr>

                                            <td>{{ $detail->idtrdpo }}</td>
                                            <td>{{ $detail->kodesubkegiatan }}</td>
                                            <td>{{ $detail->namasubkegiatan }}</td>
                                            <td>{{ $detail->kodeakun }}</td>
                                            <td>{{ $detail->namaakun }}</td>
                                            <td>{{ $detail->kodebarang }}</td>
                                            <td>{{ $detail->uraianbarang }}</td>
                                            <td>{{ $detail->kodesumberdana }}</td>
                                            <td>{{ $detail->namasumberdana }}</td>
                                            <td>{{ $detail->spek }}</td>
                                            <td>{{ rupiah($detail->volume1) }}</td>
                                            <td>{{ rupiah($detail->volume2) }}</td>
                                            <td>{{ rupiah($detail->volume3) }}</td>
                                            <td>{{ rupiah($detail->volume4) }}</td>
                                            <td>{{ $detail->satuan1 }}</td>
                                            <td>{{ $detail->satuan2 }}</td>
                                            <td>{{ $detail->satuan3 }}</td>
                                            <td>{{ $detail->satuan4 }}</td>
                                            <td>{{ rupiah($detail->harga) }}</td>
                                            <td>{{ rupiah($detail->nilai) }}</td>
                                            <td>{{ $detail->nomorpo }}</td>
                                            <td>{{ $detail->header }}</td>
                                            <td>{{ $detail->subheader }}</td>
                                            <td>
                                                @if ($cekKontrakAdendum == 0)
                                                    <a href="javascript:void(0);"
                                                        onclick="hapusRincian('{{ $detail->idtrdpo }}','{{ $detail->nilai }}')"
                                                        class="btn btn-danger btn-sm"><i
                                                            class="fadeIn animated bx bx-trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    @include('kontrak.js.edit')
@endpush
