<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Kontrak</title>
</head>

<body>
    <table style="width: 100%;text-align:center;font-weight:bold;font-size:16px">
        <tr>
            <td>
                PEMERINTAH PROVINSI KALIMANTAN BARAT <br>
                {{ $dataKontrak->namaskpd }} <br>
                {{ $dataSkpd->alamat }}
            </td>
        </tr>
    </table>

    <hr>

    <table style="width: 100%;text-align:center;font-weight:bold;font-size:14px">
        <tr>
            <td>
                RINGKASAN KONTRAK/SPK
            </td>
        </tr>
    </table>

    <br>

    <table style="width: 100%;font-size:14px;border-collapse:collapse" border="1">
        <tr>
            <th>No.</th>
            <th>Uraian</th>
            <th>:</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td style="text-align: center">1.</td>
            <td>Nama SKPD/Unit SKPD</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->namaskpd }}</td>
        </tr>
        <tr>
            <td style="text-align: center">2.</td>
            <td>Nomor dan tanggal DPA/DPPA</td>
            <td style="text-align: center">:</td>
            <td>
                {{ $dataDpa->no_dpa }} <br>
                Tanggal {{ \Carbon\Carbon::parse($dataDpa->tgl_dpa)->locale('id')->isoformat('DD MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center">3.</td>
            <td>Nama Program</td>
            <td style="text-align: center">:</td>
            <td>{{ namaProgram(Str::of($dataKegiatan->kodesubkegiatan)->substr(0, 7)) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">4.</td>
            <td>Nama Kegiatan</td>
            <td style="text-align: center">:</td>
            <td>{{ namaKegiatan(Str::of($dataKegiatan->kodesubkegiatan)->substr(0, 12)) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">5.</td>
            <td>Nama Sub Kegiatan</td>
            <td style="text-align: center">:</td>
            <td>{{ namaSubKegiatan($dataKegiatan->kodesubkegiatan) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">6.</td>
            <td>Rekening Kegiatan dan Belanja</td>
            <td style="text-align: center">:</td>
            <td>
                @foreach ($dataRekening as $rekening)
                    {{ $rekening->kodesubkegiatan }} {{ $rekening->kodeakun }} <br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="text-align: center">7.</td>
            <td>Nomor dan Tanggal Surat Perjanjian Kerja/SPK/Pesanan/PKS/PO</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->nomorkontrak }},
                {{ \Carbon\Carbon::parse($dataKontrak->tanggalkontrak)->locale('id')->isoFormat('DD MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td style="text-align: center">8.</td>
            <td>Nomor dan Tanggal SPMK/SP</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->nomorpesanan }},
                {{ \Carbon\Carbon::parse($dataKontrak->tanggalkontrak)->locale('id')->isoFormat('DD MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td style="text-align: center">9.</td>
            <td>Nama Rekanan/Pihak Ketiga</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->pihakketiga }}</td>
        </tr>
        <tr>
            <td style="text-align: center">10.</td>
            <td>Nama Perusahaan</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->namaperusahaan }}</td>
        </tr>
        <tr>
            <td style="text-align: center">11.</td>
            <td>Alamat Perusahaan</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->alamatperusahaan }}</td>
        </tr>
        <tr>
            <td style="text-align: center">12.</td>
            <td>Nilai Pekerjaan/Nilai SPK</td>
            <td style="text-align: center">:</td>
            <td>Rp. {{ number_format($dataKontrak->nilaikontrak, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: center"></td>
            <td style="vertical-align: top">
                Uraian/Volume Pekerjaan <br><br>
                Pengadaan barang/jasa
            </td>
            <td style="text-align: center">:</td>
            <td>
                {{ $dataKontrak->pekerjaan }} <br><br>
                <table style="width: 100%;border-collapse:collapse" border="1">
                    <tr>
                        <th>No.</th>
                        <th>Uraian</th>
                        <th>Volume</th>
                        <th>Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                    </tr>
                    {{-- @php
                        $total = 0;
                    @endphp
                    @foreach ($dataRekening as $item)
                        @php
                            $cek = [$item->volume1, $item->volume2, $item->volume3, $item->volume4];
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
                            $total += $item->nilai;
                        @endphp
                        <tr>
                            <td style="text-align: center">{{ $loop->iteration }}</td>
                            <td>{{ $item->uraianbarang }}</td>
                            <td style="text-align: center">{{ $volume }}</td>
                            <td style="text-align: right">{{ number_format($item->harga, 2) }}</td>
                            <td style="text-align: right">{{ number_format($item->nilai, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center">Jumlah (termasuk Pajak PPn, PPh, Biaya lainnya)
                        </td>
                        <td style="text-align: right">{{ number_format($total, 2) }}</td>
                    </tr> --}}

                    @php
                        $totalNilai = 0;
                        $totalVolume = 0;
                        $totalHarga = 0;
                        $no = 0;
                    @endphp
                    @foreach ($dataDetailRekening as $item)
                        @if ($item->urut == '1')
                            <tr>
                                <td style="text-align: center"><b>{{ ++$no }}</b></td>
                                <td><b>{{ $item->uraianbarang }}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @elseif ($item->urut == '2')
                            @php
                                $totalNilai += $item->nilai;
                                $totalVolume += $item->volume;
                                $totalHarga += $item->harga;
                            @endphp
                            <tr>
                                <td></td>
                                <td>- {{ $item->uraianbarang }}</td>
                                <td style="text-align: right">{{ number_format($item->volume, 2) }}</td>
                                <td style="text-align: center">{{ $item->satuan }}</td>
                                <td style="text-align: right">{{ number_format($item->harga, 2) }}</td>
                                <td style="text-align: right">{{ number_format($item->nilai, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <b>Jumlah (termasuk Pajak PPn, PPh, Biaya lainnya)</b>
                        </td>
                        <td style="text-align: right"><b>{{ number_format($totalVolume, 2) }}</b></td>
                        <td></td>
                        <td style="text-align: right"><b>{{ number_format($totalHarga, 2) }}</b></td>
                        <td style="text-align: right"><b>{{ number_format($totalNilai, 2) }}</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: center">13.</td>
            <td>Cara Pembayaran</td>
            <td style="text-align: center">:</td>
            <td>
                @switch($dataKontrak->carapembayaran)
                    @case('1')
                        Sekaligus
                    @break

                    @case('2')
                        Bertahap
                    @break

                    @case('3')
                        Termin
                    @break

                    @default
                @endswitch
            </td>
        </tr>
        <tr>
            <td style="text-align: center">14.</td>
            <td>Jumlah yang dibayarkan</td>
            <td style="text-align: center">:</td>
            <td>Rp. {{ number_format($dataKontrak->nilaikontrak, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">15.</td>
            <td>Jangka waktu pelaksanaan pekerjaan</td>
            <td style="text-align: center">:</td>
            <td>
                @php
                    $tanggalakhir = \Carbon\Carbon::parse($dataKontrak->tanggalakhir);
                    $tanggalawal = \Carbon\Carbon::parse($dataKontrak->tanggalawal);
                    $jarak =
                        $tanggalakhir->diffInDays($tanggalawal) === 0 ? 1 : $tanggalakhir->diffInDays($tanggalawal) + 1;
                @endphp

                {{ $jarak }} ({{ depan($jarak) }} ) hari kalendar <br>
                Tanggal {{ \Carbon\Carbon::parse($dataKontrak->tanggalawal)->locale('id')->isoformat('DD MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center">16.</td>
            <td>Tanggal penyelesaian pekerjaan</td>
            <td style="text-align: center">:</td>
            <td>{{ \Carbon\Carbon::parse($dataKontrak->tanggalakhir)->locale('id')->isoformat('DD MMMM Y') }}</td>
        </tr>
        <tr>
            <td style="text-align: center">17.</td>
            <td>Jangka waktu pemeliharaan</td>
            <td style="text-align: center">:</td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align: center">18.</td>
            <td>Ketentuan sanksi</td>
            <td style="text-align: center">:</td>
            <td>{{ $dataKontrak->ketentuansanksi }}</td>
        </tr>
    </table>

    <br>

    <table style="width: 100%;font-size:14px;text-align:center">
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%">Pontianak,
                {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->isoformat('DD MMMM Y') }} <br>
                {{ $dataTtd->jabatan }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%;padding:40px 0px">

            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%">
                <b><u> {{ $dataTtd->nama }}</u></b> <br>
                {{ $dataTtd->pangkat }} <br>
                NIP. {{ $dataTtd->nip }}
            </td>
        </tr>
    </table>
</body>

</html>
