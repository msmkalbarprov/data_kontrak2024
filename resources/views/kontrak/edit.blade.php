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
            <h6 class="mb-0 text-uppercase">Ubah Kontrak</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Jenis</label>
                            <select class="form-select select_option" id="jenis">
                                <option value="" selected selected>Silahkan Pilih</option>
                                <option value="1" {{ $kontrak->jenisspp == 1 ? 'selected' : '' }}>UP/GU</option>
                                <option value="5" {{ $kontrak->jenisspp == 5 ? 'selected' : '' }}>LS BARJAS</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Id Kontrak</label>
                        <input class="form-control" type="text" readonly disabled id="id_kontrak"
                            value="{{ $kontrak->idkontrak }}">
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Tipe</label>
                            <select class="form-select select_option" id="tipe">
                                <option value="" selected selected>Silahkan Pilih</option>
                                <option value="1" {{ $kontrak->tipe == 1 ? 'selected' : '' }}>KONTRAK</option>
                                <option value="2" {{ $kontrak->tipe == 2 ? 'selected' : '' }}>PESANAN</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3" id="pesanan">
                        <div class="col-12">
                            <label class="form-label">No. Pesanan</label>
                            <input class="form-control" type="text" id="no_pesanan"
                                placeholder="Isi dengan nomor pesanan" autofocus value="{{ $kontrak->nomorpesanan }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label kontrak">No. Kontrak</label>
                            <input class="form-control kontrak" type="text" id="no_kontrak"
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
                    {{-- <div class="row mb-3">
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
                    </div> --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Nama Pihak Ketiga</label>
                            <input class="form-control" type="text" id="pihak_ketiga"
                                placeholder="Isi dengan nama pihak ketiga" value="{{ $kontrak->pihakketiga }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Nama Perusahaan</label>
                            <input class="form-control" type="text" id="nama_perusahaan"
                                placeholder="Isi dengan nama perusahaan" value="{{ $kontrak->namaperusahaan }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Alamat Perusahaan</label>
                            <textarea class="form-control" id="alamat_perusahaan" placeholder="Isi dengan alamat perusahaan">{{ $kontrak->alamatperusahaan }}</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Awal</label>
                            <input class="form-control" type="date" id="tanggal_awal"
                                value="{{ $kontrak->tanggalawal }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input class="form-control" type="date" id="tanggal_akhir"
                                value="{{ $kontrak->tanggalakhir }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Metode Pengadaan</label>
                            <select class="form-select select_option" id="metode">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="1" {{ $kontrak->metodepengadaan == 1 ? 'selected' : '' }}>Melalui
                                    Penyedia (Tender, E Purchasing, Pengadaan Langsung)
                                </option>
                                <option value="2" {{ $kontrak->metodepengadaan == 2 ? 'selected' : '' }}>Swakelola
                                </option>
                                <option value="3" {{ $kontrak->metodepengadaan == 3 ? 'selected' : '' }}>Pengadaan
                                    yang Dikecualikan</option>
                                <option value="4" {{ $kontrak->metodepengadaan == 4 ? 'selected' : '' }}>Pengadaan
                                    Dalam Keadaan Darurat</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            {{-- <label class="form-label">Cara Pembayaran</label>
                            <textarea class="form-control" id="pembayaran" placeholder="Isi dengan cara pembayaran">{{ $kontrak->carapembayaran }}</textarea> --}}
                            <label class="form-label">Cara Pembayaran</label>
                            <select class="form-select select_option" id="pembayaran">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="1" {{ $kontrak->carapembayaran == '1' ? 'selected' : '' }}>Sekaligus
                                </option>
                                <option value="2" {{ $kontrak->carapembayaran == '2' ? 'selected' : '' }}>Bertahap
                                </option>
                                <option value="3" {{ $kontrak->carapembayaran == '3' ? 'selected' : '' }}>Termin
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Ketentuan Sanksi</label>
                            <textarea class="form-control" id="sanksi" placeholder="Isi dengan ketentuan sanksi">{{ $kontrak->ketentuansanksi }}</textarea>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        @if ($cekKontrakAdendum == 0 && $cekBast == 0)
                            <button class="btn btn-primary" id="simpan">Simpan</button>
                        @endif
                        <a href="{{ route('kontrak.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Rincian Kontrak
                    @if ($cekKontrakAdendum == 0 && $cekBast == 0)
                        <button class="btn btn-success btn-md float-end" id="tambah_rincian">Tambah</button>
                    @endif
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
                        {{-- <tbody>
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
                                        @if ($cekKontrakAdendum == 0 && $cekBast == 0)
                                            <a href="javascript:void(0);"
                                                onclick="hapusRincian('{{ $detail->idtrdpo }}','{{ $detail->nilai }}')"
                                                class="btn btn-danger btn-sm"><i
                                                    class="fadeIn animated bx bx-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> --}}
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label class="col-md-8 col-form-label kanan">Total
                            Rincian Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control kanan" id="total_rincian_kontrak"
                                style="background-color:white;border:none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Rincian Detail Kontrak
                    @if ($cekKontrakAdendum == 0 && $cekBast == 0)
                        <button class="btn btn-success btn-md float-end" id="tambah_detail_rincian">Tambah</button>
                    @endif
                </div>
                <div class="card-body table-responsive">
                    <table class="table align-middle mb-0" id="detail_rincian_kontrak" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Kode Sub Kegiatan</th>
                                <th>Kode Rekening</th>
                                <th>Kode Barang</th>
                                <th>Uraian</th>
                                <th>Volume</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label class="col-md-8 col-form-label kanan">Total
                            Rincian Detail Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control kanan" id="total_rincian_detail_kontrak"
                                style="background-color:white;border:none">
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

                    <div class="row mb-3" id="kolom_sertifikat">
                        <label class="form-label text-center"><b>Sertifikat</b></label>
                        <label class="form-label col-md-2">No. Sertifikat</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nomor_sertifikat">
                        </div>
                        <label class="form-label col-md-2">Tanggal Sertifikat</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tanggal_sertifikat">
                        </div>
                    </div>
                    <div class="row mb-3" id="kolom_lokasi">
                        <label class="form-label text-center"><b>Lokasi/Alamat</b></label>
                        <label class="form-label col-md-2">Status Tanah</label>
                        <div class="col-md-4">
                            <select class="form-select select_modal" id="status_tanah" data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="hak_milik">Hak Milik</option>
                                <option value="hak_pakai">Hak Pakai</option>
                            </select>
                        </div>
                        <label class="form-label col-md-2">Penggunaan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="penggunaan">
                        </div>
                    </div>
                    <div class="row mb-3" id="kolom_luas">
                        <label class="form-label col-md-2">Panjang</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="panjang"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                        <label class="form-label col-md-2">Lebar</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="lebar"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                        <label class="form-label col-md-2">Luas</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="luas" disabled readonly
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div id="kolom_nomor">
                        <div class="row mb-3">
                            <label class="form-label text-center"><b>Nomor:</b></label>
                            <label class="form-label col-md-2">Merk/Type</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="merk">
                            </div>
                            <label class="form-label col-md-2">Ukuran</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="ukuran">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">Pabrik</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="pabrik">
                            </div>
                            <label class="form-label col-md-2">Rangka</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="rangka">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">Mesin</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="mesin">
                            </div>
                            <label class="form-label col-md-2">Polisi</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="polisi">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">BPKB</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="bpkb">
                            </div>
                            <label class="form-label col-md-2">Bahan</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="bahan">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3" id="kolom_bangunan">
                        <label class="form-label col-md-2">Kontruksi Bangunan</label>
                        <div class="col-md-2">
                            <input type="checkbox" id="bertingkat"> Bertingkat
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox" id="beton"> Beton
                        </div>
                    </div>
                    <div id="kolom_buku">
                        <div class="row mb-3">
                            <label class="form-label text-center"><b>Buku / Perpustakaan</b></label>
                            <label class="form-label col-md-2">Judul</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="judul">
                            </div>
                            <label class="form-label col-md-2">Pencipta</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="pencipta">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">Spesifikasi</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="spesifikasi">
                            </div>
                        </div>
                    </div>
                    <div id="kolom_barang">
                        <div class="row mb-3">
                            <label class="form-label text-center"><b>Barang Bercorak</b></label>
                            <label class="form-label col-md-2">Asal Daerah</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="asal_daerah">
                            </div>
                            <label class="form-label col-md-2">Pencipta</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="pencipta_daerah">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">Bahan</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="bahan_daerah">
                            </div>
                        </div>
                    </div>
                    <div id="kolom_hewan">
                        <div class="row mb-3">
                            <label class="form-label text-center"><b>Hewan/Ternak Tumbuhan</b></label>
                            <label class="form-label col-md-2">Jenis</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="jenis_hewan">
                            </div>
                            <label class="form-label col-md-2">Ukuran</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="ukuran_hewan">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">NIK</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="nik_hewan">
                            </div>
                        </div>
                    </div>
                    <div id="kolom_aplikasi">
                        <div class="row mb-3">
                            <label class="form-label col-md-2">Nama Aplikasi</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="nama_aplikasi">
                            </div>
                            <label class="form-label col-md-2">Judul Aplikasi</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="judul_aplikasi">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label col-md-2">Pencipta Aplikasi</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="pencipta_aplikasi">
                            </div>
                            <label class="form-label col-md-2">Spesifikasi Aplikasi</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="spesifikasi_aplikasi">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="form-label col-md-2">Volume 1</label>
                        <div class="col-md-2">
                            <input class="form-control kanan" type="text" readonly disabled id="volume1">
                        </div>
                        {{-- <label class="form-label col-md-2">Satuan 1</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan1">
                        </div> --}}
                        <label class="form-label col-md-2">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume1">
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
                        {{-- <label class="form-label col-md-2">Satuan 2</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan2">
                        </div> --}}
                        <label class="form-label col-md-2">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume2">
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
                        {{-- <label class="form-label col-md-2">Satuan 3</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan3">
                        </div> --}}
                        <label class="form-label col-md-2">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume3">
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
                        {{-- <label class="form-label col-md-2">Satuan 4</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="satuan4">
                        </div> --}}
                        <label class="form-label col-md-2">Realisasi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" readonly disabled id="realisasi_volume4">
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
                        <label class="form-label col-md-2">Harga Nego</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control kanan" id="harga_nego"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-8"></label>
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
                                id="total_detail_kontrak" style="background-color:white;border:none">
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
                                        <th>Detail Kontrak</th>
                                        <th>Detail Kontrak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                {{-- <tbody>
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
                                            <td>{{ $detail->detailkontrak }}</td>
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
                                </tbody> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_detail_rincian">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rincian Detail Kontrak</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Kode Sub Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal_detail" id="kd_sub_kegiatan_detail"
                                data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Kode Rekening</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal_detail" id="kd_rek6_detail"
                                data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Kode Barang</label>
                        <div class="col-md-10">
                            <select class="form-select select_modal_detail" id="kd_barang_detail"
                                data-nama_modal="modal_rincian">
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Uraian</label>
                        <div class="col-md-10">
                            <textarea class="form-control" id="uraian_detail"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Volume</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control kanan" id="volume_detail"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Satuan</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="satuan_detail">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label col-md-2">Harga</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control kanan" id="harga_detail"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-success" id="simpan_rincian_detail">Simpan</button>
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Kembali</button>
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
