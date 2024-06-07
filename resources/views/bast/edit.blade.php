@extends('template.app')
@section('konten')
    <div class="row">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        <div class="col-xl-10 mx-auto">
            <h6 class="mb-0 text-uppercase">Edit BAST</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Kontrak</label>
                            <select class="form-select select_option" id="kontrak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_kontrak_awal as $kontrak_awal)
                                    <option value="{{ $kontrak_awal->nomorkontrak }}"
                                        data-pekerjaan="{{ $kontrak_awal->pekerjaan }}"
                                        data-rekanan="{{ $kontrak_awal->rekanan }}"
                                        data-pimpinan="{{ $kontrak_awal->pimpinan }}"
                                        data-kodeskpd="{{ $kontrak_awal->kodeskpd }}"
                                        data-id_kontrak="{{ $kontrak_awal->idkontrak }}"
                                        data-jns_ang="{{ $kontrak_awal->jns_ang }}"
                                        data-realisasi_fisik_lalu="{{ $kontrak_awal->realisasi_fisik_lalu }}"
                                        data-pihakketiga="{{ $kontrak_awal->pihakketiga }}"
                                        data-namaperusahaan="{{ $kontrak_awal->namaperusahaan }}"
                                        data-alamatperusahaan="{{ $kontrak_awal->alamatperusahaan }}"
                                        data-tanggalawal="{{ $kontrak_awal->tanggalawal }}"
                                        data-tanggalakhir="{{ $kontrak_awal->tanggalakhir }}"
                                        data-ketentuansanksi="{{ $kontrak_awal->ketentuansanksi }}"
                                        {{ $kontrak_awal->nomorkontrak == $dataBast->nomorkontrak ? 'selected' : '' }}>
                                        {{ $kontrak_awal->nomorkontrak }}
                                        | {{ $kontrak_awal->tanggalkontrak }} | {{ rupiah($kontrak_awal->nilaikontrak) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Jenis</label>
                            <select class="form-select select_option" id="jenis_kontrak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value=" " {{ $dataBast->jenis == '' ? 'selected' : '' }}>Tanpa Termin / Sekali
                                    Pembayaran</option>
                                <option value="1" {{ $dataBast->jenis == 1 ? 'selected' : '' }}>Konstruksi Dalam
                                    Pengerjaan
                                </option>
                                <option value="2" {{ $dataBast->jenis == 2 ? 'selected' : '' }}>Uang Muka</option>
                                <option value="3" {{ $dataBast->jenis == 3 ? 'selected' : '' }}>Hutang Tahun Lalu
                                </option>
                                <option value="4" {{ $dataBast->jenis == 4 ? 'selected' : '' }}>Perbulan</option>
                                <option value="5" {{ $dataBast->jenis == 5 ? 'selected' : '' }}>Bertahap</option>
                                <option value="6" {{ $dataBast->jenis == 6 ? 'selected' : '' }}>Berdasarkan Progres /
                                    Pengajuan Pekerjaan</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">No. Pesanan</label>
                            <input class="form-control" type="text" id="no_pesanan"
                                placeholder="Isi dengan nomor Pesanan" autofocus value="{{ $dataBast->nomorpesanan }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Pesanan</label>
                            <input class="form-control" type="date" id="tgl_pesanan"
                                value="{{ $dataBast->tanggalpesanan }}">
                        </div>
                    </div> --}}
                    <div class="row mb-3" id="bast">
                        <div class="col-6">
                            <label class="form-label">No. BAST</label>
                            <input class="form-control" type="text" id="no_bast" placeholder="Isi dengan nomor bast"
                                autofocus value="{{ $dataBast->nomorbapbast }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal BAST</label>
                            <input class="form-control" type="date" id="tgl_bast"
                                value="{{ $dataBast->tanggalbapbast }}">
                        </div>
                    </div>
                    <div class="row mb-3" id="bap">
                        <div class="col-6">
                            <label class="form-label">No. BAP</label>
                            <input class="form-control" type="text" id="no_bap" placeholder="Isi dengan nomor bap"
                                autofocus value="{{ $dataBast->nomorbapbast }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal BAP</label>
                            <input class="form-control" type="date" id="tgl_bap"
                                value="{{ $dataBast->tanggalbapbast }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Kode SKPD/UNIT</label>
                            <input class="form-control" type="text" readonly disabled id="kd_skpd"
                                value="{{ $skpd->kd_skpd }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Nama SKPD/UNIT</label>
                            <input class="form-control" type="text" readonly disabled id="nm_skpd"
                                value="{{ $skpd->nm_skpd }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Nama Pekerjaan</label>
                            <textarea class="form-control" id="nm_kerja" placeholder="Isi dengan nama pekerjaan" readonly disabled></textarea>
                        </div>
                    </div>
                    {{-- <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">(Rekanan) Nama Pemilik Rekening</label>
                            <select class="form-select select_option" id="rekanan">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->nmrekan }}" data-rekening="{{ $rekening->rekening }}"
                                        data-bank="{{ $rekening->bank }}" data-nm_bank="{{ $rekening->nm_bank }}"
                                        data-npwp="{{ $rekening->npwp }}">
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
                            <input class="form-control" type="text" id="no_rekening" readonly disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label">NPWP</label>
                            <input class="form-control" type="text" id="npwp" readonly disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Pimpinan</label>
                            <input class="form-control" type="text" id="pimpinan"
                                placeholder="Isi dengan nama pimpinan" readonly disabled>
                        </div>
                        <div class="col-1">
                            <label class="form-label">Bank</label>
                            <input class="form-control" type="text" id="bank" readonly disabled>
                        </div>
                        <div class="col-5">
                            <label class="form-label">Nama Bank</label>
                            <input class="form-control" type="text" id="nm_bank" readonly disabled>
                        </div>
                    </div> --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Nama Pihak Ketiga</label>
                            <input class="form-control" type="text" id="pihak_ketiga"
                                placeholder="Isi dengan nama pihak ketiga" readonly disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Nama Perusahaan</label>
                            <input class="form-control" type="text" id="nama_perusahaan"
                                placeholder="Isi dengan nama perusahaan" readonly disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Alamat Perusahaan</label>
                            <textarea class="form-control" id="alamat_perusahaan" placeholder="Isi dengan alamat perusahaan" readonly disabled></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input class="form-control" type="date" id="tanggal_awal" readonly disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input class="form-control" type="date" id="tanggal_akhir" readonly disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Ketentuan Sanksi</label>
                            <textarea class="form-control" id="sanksi" placeholder="Isi dengan ketentuan sanksi" readonly disabled></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select class="form-select select_option" id="status_kontrak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1" {{ $dataBast->statuspekerjaan == 1 ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="2" {{ $dataBast->statuspekerjaan == 2 ? 'selected' : '' }}>Belum
                                    Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label">Realisasi Fisik Lalu</label>
                            <input type="text" class="form-control kanan" id="realisasi_fisik_lalu"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" disabled readonly>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Realisasi Fisik</label>
                            <input type="text" class="form-control kanan" id="realisasi_fisik"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" disabled readonly
                                value="{{ $dataBast->realisasifisik }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Total Realisasi Fisik</label>
                            <input type="text" class="form-control kanan" id="total_realisasi_fisik"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" disabled readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" placeholder="Isi dengan keterangan" rows="5">{{ $dataBast->keterangan }}</textarea>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" id="simpan"
                            {{ $cekPenagihan > 0 ? 'hidden' : '' }}>Simpan</button>
                        <a href="{{ route('bast.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Rincian BAST
                    <button class="btn btn-success btn-md float-end" id="tambah_rincian"
                        {{ $cekPenagihan > 0 ? 'hidden' : '' }}>Tambah</button>
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
                            @foreach ($detailBast as $detail)
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
                                        {{-- @if ($cekKontrakAdendum == 0 && $cekBast == 0) --}}
                                        <a href="javascript:void(0);"
                                            onclick="hapusRincian('{{ $detail->idtrdpo }}','{{ $detail->nilai }}')"
                                            class="btn btn-danger btn-sm" {{ $cekPenagihan > 0 ? 'hidden' : '' }}><i
                                                class="fadeIn animated bx bx-trash"></i></a>
                                        {{-- @endif --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label class="col-md-8 col-form-label kanan">Total
                            Rincian BAST</label>
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
                    <h5 class="modal-title">Tambah Rincian BAST</h5>
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
                        <label class="form-label col-md-1">Volume 1</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume1">
                        </div>
                        <label class="form-label col-md-1">Satuan 1</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan1">
                        </div>
                        <label class="form-label col-md-1">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume1">
                        </div>
                        <label class="form-label col-md-1">Input Volume 1</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume1"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-1">Volume 2</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume2">
                        </div>
                        <label class="form-label col-md-1">Satuan 2</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan2">
                        </div>
                        <label class="form-label col-md-1">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume2">
                        </div>
                        <label class="form-label col-md-1">Input Volume 2</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume2"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-1">Volume 3</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume3">
                        </div>
                        <label class="form-label col-md-1">Satuan 3</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan3">
                        </div>
                        <label class="form-label col-md-1">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume3">
                        </div>
                        <label class="form-label col-md-1">Input Volume 3</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="input_volume3"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-1">Volume 4</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume4">
                        </div>
                        <label class="form-label col-md-1">Satuan 4</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan4">
                        </div>
                        <label class="form-label col-md-1">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume4">
                        </div>
                        <label class="form-label col-md-1">Input Volume 4</label>
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
                            Rincian BAST</label>
                        <div class="col-md-3">
                            <input type="text" width="100%" class="form-control kanan" readonly
                                id="total_detail_kontrak" style="background-color:white;border:none"
                                value="{{ rupiah($total) }}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-success" id="simpan_rincian">Simpan</button>
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Kembali</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Rincian BAST
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
                                    @foreach ($detailBast as $detail)
                                        <tr>
                                            <td>{{ $detail->idtrdpo }}</td>
                                            <td>{{ $detail->kodesubkegiatan }}</td>
                                            <td>{{ namaSubKegiatan($detail->kodesubkegiatan) }}</td>
                                            <td>{{ $detail->kodeakun }}</td>
                                            <td>{{ namaRekening($detail->kodeakun) }}</td>
                                            <td>{{ $detail->kodebarang }}</td>
                                            <td>{{ $detail->uraianbarang }}</td>
                                            <td>{{ $detail->kodesumberdana }}</td>
                                            <td>{{ namaSumber($detail->kodesumberdana) }}</td>
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
                                                {{-- @if ($cekKontrakAdendum == 0 && $cekBast == 0) --}}
                                                <a href="javascript:void(0);"
                                                    onclick="hapusRincian('{{ $detail->idtrdpo }}','{{ $detail->nilai }}')"
                                                    class="btn btn-danger btn-sm"><i
                                                        class="fadeIn animated bx bx-trash"></i></a>
                                                {{-- @endif --}}
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
    @include('bast.js.edit')
@endpush
