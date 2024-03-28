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
        $kd_skpd = Auth::user()->kd_skpd;

        $data = $this->connection
            ->table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        return isset($data) ? $data->jns_ang : '0';
    }

    public function sumber($request)
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
                'a.jns_ang' => $this->statusAnggaran(),
                'b.kd_barang' => $request->kd_barang,
            ])
            ->where('b.header', 'LIKE', '%' . trim(Str::replace('[#]', '', $request->header)) . '%')
            ->where('b.sub_header', 'LIKE', '%' . trim(Str::replace('[-]', '', $request->sub_header)) . '%')
            ->select('a.sumber', 'a.nm_sumber', 'b.volume1', 'b.volume2', 'b.volume3', 'b.volume4', 'b.satuan1', 'b.satuan2', 'b.satuan3', 'b.satuan4', 'b.harga', 'b.total', 'b.id', 'b.no_po', 'b.uraian', 'b.spesifikasi')
            ->get();

        return $sumber;
    }

    // Cari Kegiatan
    public function kodeSubKegiatan(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $anggaran = $this->statusAnggaran();
        $tipe = $request->tipe;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $data = $this->connection
            ->table('trskpd as a')
            ->join('ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5', 'a.jns_ang' => $anggaran])
            ->where(function ($query) use ($tipe, $kd_sub_kegiatan) {
                if ($tipe == 'edit') {
                    $query->where('a.kd_sub_kegiatan', $kd_sub_kegiatan);
                }
            })
            ->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=a.kd_program) as nm_program"), 'a.total')
            ->get();

        return response()->json($data);
    }

    // Cari Rekening
    public function rekening(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_ang = $this->statusAnggaran();

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

        return response()->json($daftar_rekening);
    }

    // Cari Kode Barang
    public function kodeBarang(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek6 = $request->kd_rek6;
        $jns_ang = $this->statusAnggaran();

        $data = $this->connection
            ->table('trdpo_rinci as a')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.jns_ang' => $jns_ang])
            ->select('a.kd_barang', 'a.header', 'a.sub_header', 'a.uraian')
            ->get();

        return response()->json($data);
    }

    // Cari Sumber Dana
    public function sumberDana(Request $request)
    {
        return response()->json($this->sumber($request));
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
            })
            ->select('a.*')
            ->where(['b.kodeskpd' => $kd_skpd, 'b.nomorkontrak' => $kontrak_awal, 'b.idkontrak' => $id_kontrak])
            ->get();

        return response()->json($data);
    }
}
