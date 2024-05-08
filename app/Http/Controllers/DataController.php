<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataController extends Controller
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection('simakda');
    }

    // Status Anggaran
    public function statusAnggaran()
    {
        return status_anggaran();
    }

    public function cariKegiatan($request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $tipe = $request->tipe;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $status_anggaran = $request->status_anggaran;

        $data = $this
            ->connection
            ->table('simakda_2024.dbo.trskpd as a')
            ->join('simakda_2024.dbo.ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5', 'a.jns_ang' => $status_anggaran])
            ->where(function ($query) use ($tipe, $kd_sub_kegiatan) {
                if ($tipe == 'edit' || $tipe == 'adendum') {
                    $query->where('a.kd_sub_kegiatan', $kd_sub_kegiatan);
                } else {
                    $query->whereRaw("a.kd_sub_kegiatan NOT IN (SELECT c.kodesubkegiatan from data_kontrak.dbo.trdkontrak c where c.kodesubkegiatan=a.kd_sub_kegiatan and c.kodeskpd=a.kd_skpd)");
                }
            })
            ->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM simakda_2024.dbo.ms_program WHERE kd_program=a.kd_program) as nm_program"), 'a.total')
            ->get();

        return $data;
    }

    public function cariRekening($request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_ang = $request->status_anggaran;

        $daftar_rekening = $this->connection->select("SELECT a.kd_rek6,a.nm_rek6,e.map_lo,
                      (SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            trdtransout c
                        LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND d.kd_skpd = a.kd_skpd
                        AND c.kd_rek6 = a.kd_rek6
                        AND d.jns_spp='1'
                        UNION ALL
                        SELECT SUM(x.nilai) as nilai FROM trdspp x
                        INNER JOIN trhspp y
                        ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                        WHERE
                            x.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND x.kd_skpd = a.kd_skpd
                        AND x.kd_rek6 = a.kd_rek6
                        AND y.jns_spp IN ('3','4','5','6')
                        AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')

                        UNION ALL
                        SELECT SUM(nilai) as nilai FROM trdtagih t
                        INNER JOIN trhtagih u
                        ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                        WHERE
                        t.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND u.kd_skpd = a.kd_skpd
                        AND t.kd_rek = a.kd_rek6
                        AND u.no_bukti
                        NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=? )

                        -- tambahan tampungan
                        UNION ALL
                        SELECT SUM(nilai) as nilai FROM tb_transaksi
                        WHERE
                        kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND kd_skpd = a.kd_skpd
                        AND kd_rek6 = a.kd_rek6
                        -- tambahan tampungan
                        )r) AS lalu,
                    0 AS sp2d,a.nilai AS anggaran
                      FROM trdrka a LEFT JOIN ms_rek6 e ON a.kd_rek6=e.kd_rek6
                      WHERE a.kd_sub_kegiatan= ? AND jns_ang=? AND a.kd_skpd = ? and a.status_aktif='1' and (left(a.kd_rek6,2)=? or left(a.kd_rek6,4)=?)", [$kd_skpd, $kd_sub_kegiatan, $jns_ang, $kd_skpd, '52', '5102']);

        return $daftar_rekening;
    }

    public function cariKodeBarang($request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek6 = $request->kd_rek6;
        $jns_ang = $request->status_anggaran;

        $data = $this->connection
            ->table('trdpo_rinci as a')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.jns_ang' => $jns_ang])
            ->select('a.kd_barang', 'a.header', 'a.sub_header', 'a.uraian')
            ->get();

        return $data;
    }

    public function cariSumber($request)
    {
        $sumber = $this->connection
            ->table('trdpo as a')
            ->join('trdpo_rinci as b', function ($join) {
                $join->on('a.jns_ang', '=', 'b.jns_ang');
                $join->on('a.no_trdrka', '=', 'b.no_trdrka');
                $join->on('a.header', '=', 'b.header');
            })
            ->where([
                'a.kd_skpd' => Auth::user()->kd_skpd,
                'a.kd_sub_kegiatan' => $request->kd_sub_kegiatan,
                'a.kd_rek6' => $request->kd_rek6,
                'a.jns_ang' => $request->status_anggaran,
                'b.kd_barang' => $request->kd_barang,
                'b.header' => $request->header,
                'b.sub_header' => $request->sub_header,
            ])
            // ->where('b.header', 'LIKE', '%' . trim(Str::replace('[#]', '', $request->header)) . '%')
            // ->where('b.sub_header', 'LIKE', '%' . trim(Str::replace('[-]', '', $request->sub_header)) . '%')
            ->select('a.sumber', 'a.nm_sumber', 'b.volume1', 'b.volume2', 'b.volume3', 'b.volume4', 'b.satuan1', 'b.satuan2', 'b.satuan3', 'b.satuan4', 'b.harga', 'b.total', 'b.id', 'b.no_po', 'b.uraian', 'b.spesifikasi')
            ->get();

        return $sumber;
    }

    // Cari Kegiatan
    public function kodeSubKegiatan(Request $request)
    {
        $data = $this->cariKegiatan($request);

        return response()->json($data);
    }

    // Cari Rekening
    public function rekening(Request $request)
    {
        $daftar_rekening = $this->cariRekening($request);

        return response()->json($daftar_rekening);
    }

    // Cari Kode Barang
    public function kodeBarang(Request $request)
    {
        $data = $this->cariKodeBarang($request);

        return response()->json($data);
    }

    // Cari Sumber Dana
    public function sumberDana(Request $request)
    {
        return response()->json($this->cariSumber($request));
    }

    // Cari Detail Kontrak Untuk Buat Kontrak Adendum
    public function detailKontrak(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kontrak_awal = $request->kontrak_awal;
        $id_kontrak = $request->id_kontrak;

        $data = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->select('a.*')
            ->where(['b.kodeskpd' => $kd_skpd, 'b.nomorkontrak' => $kontrak_awal, 'b.idkontrak' => $id_kontrak])
            ->get();

        return response()->json($data);
    }

    // Cari Kegiatan, Rekening, Kode Barang, Sumber pada rincian kontrak awal untuk Kontrak Adendum
    public function dataAdendum(Request $request)
    {
        return response()->json([
            'kegiatan' => $this->cariKegiatan($request),
            'rekening' => $this->cariRekening($request),
            'kodeBarang' => $this->cariKodeBarang($request),
            'sumber' => $this->cariSumber($request),
        ]);
    }

    // Cari Kegiatan BAST/BAP/PESANAN
    public function kegiatanBast(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kontrak = $request->kontrak;
        $status_anggaran = $request->status_anggaran;

        $data = $this
            ->connection
            ->table('simakda_2024.dbo.trskpd as a')
            ->join('simakda_2024.dbo.ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5', 'a.jns_ang' => $status_anggaran])
            ->whereRaw("a.kd_sub_kegiatan IN (SELECT c.kodesubkegiatan from data_kontrak.dbo.trdkontrak c inner join data_kontrak.dbo.trhkontrak d on c.idkontrak=d.idkontrak and c.nomorkontrak=d.nomorkontrak and c.kodeskpd=d.kodeskpd where d.nomorkontrak=? and d.kodeskpd=?)", [$kontrak, $kd_skpd])
            ->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM simakda_2024.dbo.ms_program WHERE kd_program=a.kd_program) as nm_program"), 'a.total')
            ->get();

        return response()->json($data);
    }

    // Cari Rekening BAST/BAP/PESANAN
    public function rekeningBast(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_ang = $request->status_anggaran;
        $kontrak = $request->kontrak;

        $daftar_rekening = $this->connection->select("SELECT a.kd_rek6,a.nm_rek6,e.map_lo,
                      (SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            simakda_2024.dbo.trdtransout c
                        LEFT JOIN simakda_2024.dbo.trhtransout d ON c.no_bukti = d.no_bukti
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND d.kd_skpd = a.kd_skpd
                        AND c.kd_rek6 = a.kd_rek6
                        AND d.jns_spp='1'
                        UNION ALL
                        SELECT SUM(x.nilai) as nilai FROM simakda_2024.dbo.trdspp x
                        INNER JOIN simakda_2024.dbo.trhspp y
                        ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                        WHERE
                            x.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND x.kd_skpd = a.kd_skpd
                        AND x.kd_rek6 = a.kd_rek6
                        AND y.jns_spp IN ('3','4','5','6')
                        AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')

                        UNION ALL
                        SELECT SUM(nilai) as nilai FROM simakda_2024.dbo.trdtagih t
                        INNER JOIN simakda_2024.dbo.trhtagih u
                        ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                        WHERE
                        t.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND u.kd_skpd = a.kd_skpd
                        AND t.kd_rek = a.kd_rek6
                        AND u.no_bukti
                        NOT IN (select no_tagih FROM simakda_2024.dbo.trhspp WHERE kd_skpd=? )

                        -- tambahan tampungan
                        UNION ALL
                        SELECT SUM(nilai) as nilai FROM simakda_2024.dbo.tb_transaksi
                        WHERE
                        kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND kd_skpd = a.kd_skpd
                        AND kd_rek6 = a.kd_rek6
                        -- tambahan tampungan
                        )r) AS lalu,
                    0 AS sp2d,a.nilai AS anggaran
                      FROM simakda_2024.dbo.trdrka a LEFT JOIN simakda_2024.dbo.ms_rek6 e ON a.kd_rek6=e.kd_rek6
                      WHERE a.kd_sub_kegiatan= ? AND jns_ang=? AND a.kd_skpd = ? and a.status_aktif='1' and (left(a.kd_rek6,2)=? or left(a.kd_rek6,4)=? and a.kd_rek6 in (SELECT c.kodeakun from data_kontrak.dbo.trdkontrak c inner join data_kontrak.dbo.trhkontrak d on c.idkontrak=d.idkontrak and c.nomorkontrak=d.nomorkontrak and c.kodeskpd=d.kodeskpd where d.nomorkontrak=? and d.kodeskpd=?))", [$kd_skpd, $kd_sub_kegiatan, $jns_ang, $kd_skpd, '52', '5102', $kontrak, $kd_skpd]);

        return response()->json($daftar_rekening);
    }

    // Cari Kode Barang BAST/BAP/PESANAN
    public function barangBast(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek6 = $request->kd_rek6;
        $jns_ang = $request->status_anggaran;
        $kontrak = $request->kontrak;

        $data = $this->connection
            ->table('simakda_2024.dbo.trdpo_rinci as a')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.jns_ang' => $jns_ang])
            ->whereRaw("a.kd_barang IN (SELECT c.kodebarang from data_kontrak.dbo.trdkontrak c inner join data_kontrak.dbo.trhkontrak d on c.idkontrak=d.idkontrak and c.nomorkontrak=d.nomorkontrak and c.kodeskpd=d.kodeskpd where d.nomorkontrak=? and d.kodeskpd=? and a.header=c.header and a.sub_header=c.subheader)", [$kontrak, $kd_skpd])
            ->select('a.kd_barang', 'a.header', 'a.sub_header', 'a.uraian')
            ->get();

        return response()->json($data);
    }

    // Cari Sumber Dana BAST/BAP/PESANAN
    public function sumberBast(Request $request)
    {
        $data = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where([
                'a.kodeskpd' => Auth::user()->kd_skpd,
                'a.kodesubkegiatan' => $request->kd_sub_kegiatan,
                'a.kodeakun' => $request->kd_rek6,
                'a.kodebarang' => $request->kd_barang,
                'a.header' => $request->header,
                'a.subheader' => $request->sub_header,
                'a.nomorkontrak' => $request->kontrak,
            ])
            ->select('a.kodesumberdana as sumber', 'a.namasumberdana as nm_sumber', 'a.volume1', 'a.volume2', 'a.volume3', 'a.volume4', 'a.satuan1', 'a.satuan2', 'a.satuan3', 'a.satuan4', 'a.harga', 'a.nilai as total', 'a.idtrdpo as id', 'a.nomorpo as no_po', 'a.uraianbarang as uraian', 'a.spek as spesifikasi')
            ->get();

        return response()->json($data);
    }

    public function realisasiBast(Request $request)
    {
        $data = DB::table('trdbapbast as a')
            ->join('trhbast as b', function ($join) {
                $join->on('a.nomorpesanan', '=', 'b.nomorpesanan');
                $join->on('a.nomorbapbast', '=', 'b.nomorbapbast');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where([
                'a.kodeskpd' => Auth::user()->kd_skpd,
                'a.kodesubkegiatan' => $request->kd_sub_kegiatan,
                'a.kodeakun' => $request->kd_rek6,
                'a.kodebarang' => $request->kd_barang,
                'a.header' => $request->header,
                'a.subheader' => $request->sub_header,
                'a.kodesumberdana' => $request->sumber,
                // 'b.idkontrak' => $request->id_kontrak,
            ])
            ->selectRaw("ISNULL(sum(volume1),0) as volume1,ISNULL(sum(volume2),0) as volume2,ISNULL(sum(volume3),0) as volume3,ISNULL(sum(volume4),0) as volume4")
            ->first();

        return response()->json($data);
    }

    // CEK KONTRAK KETIKA DIPILIH UNTUK MEMBUAT BAST
    public function cekKontrak(Request $request)
    {
        $rincianKontrak = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where([
                'a.kodeskpd' => Auth::user()->kd_skpd,
                'a.nomorkontrak' => $request->kontrak,
            ])
            ->select('a.*')
            ->get();

        foreach ($rincianKontrak as $rincian) {
            $dataKontrak = DB::table('trdkontrak as a')
                ->join('trhkontrak as b', function ($join) {
                    $join->on('a.idkontrak', '=', 'b.idkontrak');
                    $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                    $join->on('a.kodeskpd', '=', 'b.kodeskpd');
                })
                ->where([
                    'a.kodeskpd' => $rincian->kodeskpd,
                    'a.kodesubkegiatan' => $rincian->kodesubkegiatan,
                    'a.kodeakun' => $rincian->kodeakun,
                    'a.kodebarang' => $rincian->kodebarang,
                    'a.header' => $rincian->header,
                    'a.subheader' => $rincian->subheader,
                    'a.nomorkontrak' => $rincian->nomorkontrak,
                ])
                ->select('a.kodesumberdana as sumber', 'a.namasumberdana as nm_sumber', 'a.volume1', 'a.volume2', 'a.volume3', 'a.volume4', 'a.satuan1', 'a.satuan2', 'a.satuan3', 'a.satuan4', 'a.harga', 'a.nilai as total', 'a.idtrdpo as id', 'a.nomorpo as no_po', 'a.uraianbarang as uraian', 'a.spek as spesifikasi')
                ->first();

            $dataAnggaranSaatIni = $this->connection
                ->table('trdpo as a')
                ->join('trdpo_rinci as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.jns_ang');
                    $join->on('a.no_trdrka', '=', 'b.no_trdrka');
                    $join->on('a.header', '=', 'b.header');
                })
                ->where([
                    'a.kd_skpd' => $rincian->kodeskpd,
                    'a.kd_sub_kegiatan' => $rincian->kodesubkegiatan,
                    'a.kd_rek6' => $rincian->kodeakun,
                    'a.jns_ang' => status_anggaran(),
                    'b.kd_barang' => $rincian->kodebarang,
                    'b.header' => $rincian->header,
                    'b.sub_header' => $rincian->subheader,
                ])
                ->select('a.sumber', 'a.nm_sumber', 'b.volume1', 'b.volume2', 'b.volume3', 'b.volume4', 'b.satuan1', 'b.satuan2', 'b.satuan3', 'b.satuan4', 'b.harga', 'b.total', 'b.id', 'b.no_po', 'b.uraian', 'b.spesifikasi')
                ->first();

            $message = '';

            // PROTEKSI NILAI KONTRAK MELEBIHI ANGGARAN SELANJUTNYA (AWAL)
            if (floatval($dataKontrak->volume1) > ($dataAnggaranSaatIni->volume1)) {
                $message .= "Input volume 1 melebihi anggaran kontrak volume 1 : " . rupiah($dataAnggaranSaatIni->volume1) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
            }

            if (floatval($dataKontrak->volume2) > ($dataAnggaranSaatIni->volume2)) {
                $message .= "Input volume 2 melebihi anggaran kontrak volume 2 : " . rupiah($dataAnggaranSaatIni->volume2) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
            }

            if (floatval($dataKontrak->volume3) > ($dataAnggaranSaatIni->volume3)) {
                $message .= "Input volume 3 melebihi anggaran kontrak volume 3 : " . rupiah($dataAnggaranSaatIni->volume3) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
            }

            if (floatval($dataKontrak->volume4) > ($dataAnggaranSaatIni->volume4)) {
                $message .= "Input volume 4 melebihi anggaran kontrak volume 4 : " . rupiah($dataAnggaranSaatIni->volume4) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
            }
            // PROTEKSI NILAI KONTRAK MELEBIHI ANGGARAN SELANJUTNYA (AKHIR)
        }

        if ($message == '') {
            return response()->json([
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'error' => $message
            ], 400);
        }
    }
}
