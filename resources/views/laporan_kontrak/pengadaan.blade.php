<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pengadaan Kontrak</title>
</head>

<body>
    <table style="width: 100%;text-align:center;font-weight:bold;font-size:16px">
        <tr>
            <td>
                PEMERINTAH PROVINSI KALIMANTAN BARAT <br>
                {{ $dataSkpd->nm_skpd }} <br>
                {{ $dataSkpd->alamat }}
            </td>
        </tr>
    </table>

    <hr>

    <table style="width: 100%;font-weight:bold;font-size:14px">
        <tr>
            <td>
                Nama PPK : {{ $dataPpk->nama }}
            </td>
        </tr>
    </table>

    <br>

    <table style="width: 100%;font-size:14px;border-collapse:collapse" border="1">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Rekening</th>
                <th rowspan="2">Kegiatan</th>
                <th rowspan="2">Sumber Dana</th>
                <th rowspan="2">Pagu (Rp)</th>
                <th rowspan="2">Metode <br>Pengadaan</th>
                <th colspan="7">SPK/Kontrak</th>
                <th colspan="3">Realisasi Fisik</th>
                <th colspan="2">Berita Acara Pemeriksaan</th>
                <th colspan="2">Berita Acara Serah Terima</th>
                <th colspan="5">Realisasi Keuangan</th>
                <th rowspan="2">Sisa Nilai Kontrak</th>
                <th rowspan="2">Sisa Pagu Dana</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Penyedia</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Nilai (Rp)</th>
                <th>Jangka Waktu</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Target</th>
                <th>Realisasi</th>
                <th>Deviasi</th>
                <th>No. BA-PP</th>
                <th>Tanggal</th>
                <th>No. BA-ST</th>
                <th>Tanggal</th>
                <th>Pembayaran</th>
                <th>Nomor SP2D</th>
                <th>Tanggal SP2D</th>
                <th>Nilai</th>
                <th>(%)</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
                <th>15</th>
                <th>16</th>
                <th>17</th>
                <th>18</th>
                <th>19</th>
                <th>20</th>
                <th>21</th>
                <th>22</th>
                <th>23</th>
                <th>24</th>
                <th>25</th>
                <th>26</th>
                <th>27</th>
                <th>28</th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach ($dataRincianKontrak as $item)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $item->kodeakun }}</td>
                    <td>{{ $item->namaakun }}</td>
                    <td>{{ $item->namasumberdana }}</td>
                    <td style="text-align: right">
                        {{ number_format(paguAnggaran($item), 2) }}
                    </td>
                    <td>{{ $item->metodepengadaan == 1 ? 'Kontraktual' : 'Swakelola' }}</td>
                    <td>{{ $item->namaperusahaan }}</td>
                    <td>{{ $item->nomorkontrak }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggalkontrak)->locale('id')->isoformat('DD MMMM YY') }}</td>
                    <td style="text-align: right">
                        {{ number_format($item->nilai, 2) }}
                    </td>
                    <td>
                        @php
                            $tanggalakhir = \Carbon\Carbon::parse($item->tanggalakhir);
                            $tanggalawal = \Carbon\Carbon::parse($item->tanggalawal);
                            $jarak =
                                $tanggalakhir->diffInDays($tanggalawal) === 0
                                    ? 1
                                    : $tanggalakhir->diffInDays($tanggalawal);
                        @endphp
                        {{ $jarak }} ({{ depan($jarak) }}) hari kalender
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggalawal)->locale('id')->isoformat('DD MMMM YY') }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggalakhir)->locale('id')->isoformat('DD MMMM YY') }}
                    </td>
                    <td>100%</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach --}}

            @php
                $no = 0;
            @endphp
            @foreach ($rincianKontrak as $item)
                @if ($item->urut === '1')
                    @php
                        $no += 1;
                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $no }}</td>
                        <td>{{ dotrek($item->kodeakun) }}</td>
                        <td>{{ setRekeningAkun($item->kodeakun) }}</td>
                        <td colspan="25"></td>
                    </tr>
                @elseif ($item->urut == '2' || $item->urut == '3' || $item->urut == '4')
                    <tr>
                        <td></td>
                        <td>{{ dotrek($item->kodeakun) }}</td>
                        <td>{{ setRekeningAkun($item->kodeakun) }}</td>
                        <td colspan="25"></td>
                    </tr>
                @else
                    <tr>
                        <td></td>
                        <td></td>

                        {{-- ,(select isnull(nomorbapbast,'') from trhbast where b.nomorkontrak=nomorkontrak and b.kodeskpd=kodeskpd and jenis='2') as nomorbap,
                                    (select isnull(tanggalbapbast,'') from trhbast where b.nomorkontrak=nomorkontrak and b.kodeskpd=kodeskpd and jenis='2')
as tanggalbap,(select isnull(nomorbapbast,'') from trhbast where b.nomorkontrak=nomorkontrak and b.kodeskpd=kodeskpd and jenis!='2') as nomorbast,
                                    (select isnull(tanggalbapbast,'') from trhbast where b.nomorkontrak=nomorkontrak and b.kodeskpd=kodeskpd and jenis!='2')
as tanggalbast,(select isnull(realisasifisik,0) from trhbast where b.nomorkontrak=nomorkontrak and b.kodeskpd=kodeskpd) as realisasifisik --}}
                        @php
                            ini_set('max_execution_time', -1);
                            $headerKontrak = DB::table('trhkontrak as b')
                                ->selectRaw(
                                    "b.*
                                    ,(select SUM(realisasifisik) from trhbast where b.nomorkontrak=nomorkontrak and b.kodeskpd=kodeskpd) as realisasifisik",
                                )
                                ->where([
                                    'idkontrak' => $item->idkontrak,
                                    'nomorkontrak' => $item->nomorkontrak,
                                ])
                                ->first();

                            $sumberDana = DB::table('trdkontrak as a')
                                ->join('trhkontrak as b', function ($join) {
                                    $join->on('a.idkontrak', '=', 'b.idkontrak');
                                    $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                                    $join->on('a.kodeskpd', '=', 'b.kodeskpd');
                                })
                                ->select('a.namasumberdana')
                                ->where([
                                    'a.idkontrak' => $item->idkontrak,
                                    'a.nomorkontrak' => $item->nomorkontrak,
                                    'a.kodeskpd' => $item->kodeskpd,
                                    'a.kodesubkegiatan' => $item->kodesubkegiatan,
                                    'a.kodeakun' => $item->kodeakun,
                                ])
                                ->groupby('a.namasumberdana')
                                ->get();

                            $pagu = DB::connection('simakda')
                                ->table('trdrka')
                                ->where([
                                    'kd_skpd' => $item->kodeskpd,
                                    'kd_sub_kegiatan' => $item->kodesubkegiatan,
                                    'kd_rek6' => $item->kodeakun,
                                    'jns_ang' => $headerKontrak->jns_ang,
                                ])
                                ->first();

                            $dataSp2d = DB::connection('simakda')
                                ->table('trdspp as a')
                                ->join('trhspp as b', function ($join) {
                                    $join->on('a.no_spp', '=', 'b.no_spp');
                                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                                })
                                ->join('trhsp2d as c', function ($join) {
                                    $join->on('b.no_spp', '=', 'c.no_spp');
                                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                                })
                                ->select('c.no_sp2d', 'c.tgl_sp2d')
                                ->selectRaw('sum(a.nilai) as nilai')
                                ->where([
                                    'b.kontrak' => $headerKontrak->nomorkontrak,
                                    'b.kd_skpd' => $headerKontrak->kodeskpd,
                                    'a.kd_sub_kegiatan' => $item->kodesubkegiatan,
                                    'a.kd_rek6' => $item->kodeakun,
                                    'c.status_bud' => '1',
                                ])
                                ->groupby('c.no_sp2d', 'c.tgl_sp2d')
                                ->get();

                            $dataBapBast = DB::table('trhbast')
                                ->select('nomorbapbast', 'tanggalbapbast', 'jenis')
                                ->where([
                                    'idkontrak' => $item->idkontrak,
                                    'nomorkontrak' => $item->nomorkontrak,
                                ])
                                ->get();
                        @endphp

                        <td>{{ $headerKontrak->pekerjaan }}</td>
                        <td>
                            @foreach ($sumberDana as $rincian)
                                <ul>
                                    <li>{{ $rincian->namasumberdana }}</li>
                                </ul>
                            @endforeach
                        </td>
                        <td style="text-align: right">
                            {{ number_format($pagu->nilai, 2) }}
                        </td>
                        <td>{{ $headerKontrak->metodepengadaan === '1' ? 'Kontraktual' : 'Swakelola' }}</td>
                        <td>{{ $headerKontrak->namaperusahaan }}</td>
                        <td>Nomor : {{ $headerKontrak->nomorkontrak }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($headerKontrak->tanggalkontrak)->locale('id')->isoformat('DD MMMM YYYY') }}
                        </td>
                        <td style="text-align: right">{{ number_format($item->nilai, 2) }}</td>
                        <td>
                            @php
                                $tanggalakhir = \Carbon\Carbon::parse($headerKontrak->tanggalakhir);
                                $tanggalawal = \Carbon\Carbon::parse($headerKontrak->tanggalawal);
                                $jarak =
                                    $tanggalakhir->diffInDays($tanggalawal) === 0
                                        ? 1
                                        : $tanggalakhir->diffInDays($tanggalawal);
                            @endphp
                            {{ $jarak }} hari kalender
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($headerKontrak->tanggalawal)->locale('id')->isoformat('DD MMMM YY') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($headerKontrak->tanggalakhir)->locale('id')->isoformat('DD MMMM YY') }}
                        </td>
                        <td style="text-align: center">
                            100 %
                        </td>
                        <td style="text-align: center">
                            {{ $headerKontrak->realisasifisik == '' ? 0 : $headerKontrak->realisasifisik }} %
                        </td>
                        <td style="text-align: center">
                            {{ 100 - $headerKontrak->realisasifisik }} %
                        </td>
                        {{-- <td>{{ $headerKontrak->nomorbap }}</td>
                        <td>{{ $headerKontrak->tanggalbap }}</td>
                        <td>{{ $headerKontrak->nomorbast }}</td>
                        <td>{{ $headerKontrak->tanggalbast }}</td> --}}
                        <td>
                            @foreach ($dataBapBast as $bapBast)
                                <ul>
                                    @if ($bapBast->jenis == 2)
                                        <li>{{ $bapBast->nomorbapbast }}</li>
                                    @endif
                                </ul>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($dataBapBast as $bapBast)
                                <ul>
                                    @if ($bapBast->jenis == 2)
                                        <li>
                                            {{ \Carbon\Carbon::parse($bapBast->tanggalbapbast)->locale('id')->isoformat('DD MMMM YY') }}
                                        </li>
                                    @endif
                                </ul>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($dataBapBast as $bapBast)
                                <ul>
                                    @if ($bapBast->jenis != 2)
                                        <li>{{ $bapBast->nomorbapbast }}</li>
                                    @endif
                                </ul>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($dataBapBast as $bapBast)
                                <ul>
                                    @if ($bapBast->jenis != 2)
                                        <li>
                                            {{ \Carbon\Carbon::parse($bapBast->tanggalbapbast)->locale('id')->isoformat('DD MMMM YY') }}
                                        </li>
                                    @endif
                                </ul>
                            @endforeach
                        </td>
                        <td>pembayaran</td>
                        @php
                            $total_sp2d = 0;
                        @endphp
                        @forelse ($dataSp2d as $sp2d)
                            @php
                                $total_sp2d += $sp2d->nilai;
                            @endphp
                            <td>
                                <ul>
                                    <li>{{ $sp2d->no_sp2d }}</li>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <li>{{ $sp2d->tgl_sp2d }}</li>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <li>{{ $sp2d->nilai }}</li>
                                </ul>
                            </td>
                        @empty
                            <td></td>
                            <td></td>
                            <td></td>
                        @endforelse
                        <td>
                            {{ $total_sp2d == 0 ? 0 : number_format(($total_sp2d / $item->nilai) * 100, 2) }} %
                        </td>
                        <td style="text-align: right">
                            {{ number_format($item->nilai - $total_sp2d, 2) }}
                        </td style="text-align: right">
                        <td>{{ number_format($pagu->nilai - $total_sp2d, 2) }}</td>
                        <td></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <br>

    <table style="width: 100%;font-size:14px;text-align:center">
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%">Pontianak,
                {{ \Carbon\Carbon::parse($tanggalTtd)->locale('id')->isoformat('DD MMMM Y') }} <br>
                {{ $dataPa->jabatan }}
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
                <b><u> {{ $dataPa->nama }}</u></b> <br>
                {{ $dataPa->pangkat }} <br>
                NIP. {{ $dataPa->nip }}
            </td>
        </tr>
    </table>
</body>

</html>
