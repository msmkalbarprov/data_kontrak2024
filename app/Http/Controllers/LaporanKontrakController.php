<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanKontrakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = [
            'dataTtd' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
            'dataPpk' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->whereIn('kode', ['PPK'])
                ->get()
        ];

        return view('laporan_kontrak.index')->with($data);
    }

    public function cetakRingkasan(Request $request)
    {
        $dataKontrak = $this->dataKontrak($request);

        $data = [
            'dataKontrak' => $dataKontrak,
            'dataSkpd' => DB::connection('simakda')
                ->table('ms_skpd')
                ->where(['kd_skpd' => $request->kd_skpd])
                ->first(),
            'dataDpa' => DB::connection('simakda')
                ->table('trhrka')
                ->where(['kd_skpd' => $request->kd_skpd, 'jns_ang' => $dataKontrak->jns_ang, 'status' => '1'])
                ->first(),
            'dataKegiatan' => DB::table('trdkontrak')
                ->select('kodesubkegiatan')
                ->where(['nomorkontrak' => $request->no_kontrak, 'idkontrak' => $request->id_kontrak, 'kodeskpd' => $request->kd_skpd])
                ->first(),
            'dataRekening' => DB::table('trdkontrak')
                ->where(['nomorkontrak' => $request->no_kontrak, 'idkontrak' => $request->id_kontrak, 'kodeskpd' => $request->kd_skpd])
                ->get(),
            'tanggalTtd' => $request->tanggal_ttd,
            'dataTtd' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => $request->kd_skpd, 'nip' => $request->pa_kpa])
                ->whereIn('kode', ['PA', 'KPA'])
                ->first()
        ];

        return view('laporan_kontrak.ringkasan')->with($data);
    }

    public function cetakPengadaan(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $rincianKontrak = collect(DB::select("SELECT 1 as urut,left(a.kodeakun,4) as kodeakun,'' kodesubkegiatan,''kodesumberdana,''kodeskpd,''nomorkontrak,''idkontrak,''kodebarang,''header,''subheader from trdkontrak a inner join trhkontrak b on a.idkontrak=b.idkontrak and a.nomorkontrak=b.nomorkontrak and a.kodeskpd=b.kodeskpd where b.kodeskpd=? group by left(a.kodeakun,4)
        UNION ALL
        SELECT 2 as urut,left(a.kodeakun,6) as kodeakun,'' kodesubkegiatan,''kodesumberdana,''kodeskpd,''nomorkontrak,''idkontrak,''kodebarang,''header,''subheader from trdkontrak a inner join trhkontrak b on a.idkontrak=b.idkontrak and a.nomorkontrak=b.nomorkontrak and a.kodeskpd=b.kodeskpd where b.kodeskpd=? group by left(a.kodeakun,6)
        UNION ALL
        SELECT 3 as urut,left(a.kodeakun,8) as kodeakun,'' kodesubkegiatan,''kodesumberdana,''kodeskpd,''nomorkontrak,''idkontrak,''kodebarang,''header,''subheader from trdkontrak a inner join trhkontrak b on a.idkontrak=b.idkontrak and a.nomorkontrak=b.nomorkontrak and a.kodeskpd=b.kodeskpd where b.kodeskpd=? group by left(a.kodeakun,8)
        UNION ALL
        SELECT 4 as urut,a.kodeakun as kodeakun,'' kodesubkegiatan,''kodesumberdana,''kodeskpd,''nomorkontrak,''idkontrak,''kodebarang,''header,''subheader from trdkontrak a inner join trhkontrak b on a.idkontrak=b.idkontrak and a.nomorkontrak=b.nomorkontrak and a.kodeskpd=b.kodeskpd where b.kodeskpd=? group by a.kodeakun
        UNION ALL
        SELECT 5 as urut,a.kodeakun as kodeakun,a.kodesubkegiatan,a.kodesumberdana,a.kodeskpd,b.nomorkontrak,b.idkontrak,a.kodebarang,a.header,a.subheader from trdkontrak a inner join trhkontrak b on a.idkontrak=b.idkontrak and a.nomorkontrak=b.nomorkontrak and a.kodeskpd=b.kodeskpd where b.kodeskpd=? ORDER BY kodeakun,urut", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]));

        // dd($rincianKontrak);
        $data = [
            'rincianKontrak' => $rincianKontrak,
            'dataSkpd' => DB::connection('simakda')
                ->table('ms_skpd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->first(),
            // 'dataRincianKontrak' => DB::table('trdkontrak as a')
            //     ->join('trhkontrak as b', function ($join) {
            //         $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
            //         $join->on('a.idkontrak', '=', 'b.idkontrak');
            //         $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            //     })
            //     ->select('a.*', 'b.metodepengadaan', 'b.jns_ang', 'b.namaperusahaan', 'b.tanggalkontrak', 'b.tanggalawal', 'b.tanggalakhir')
            //     ->where(['b.nomorkontrak' => $request->no_kontrak, 'b.idkontrak' => $request->id_kontrak, 'b.kodeskpd' => $request->kd_skpd])
            //     ->get(),
            'tanggalTtd' => $request->tanggal_ttd,
            'dataPa' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd, 'nip' => $request->pa_kpa])
                ->whereIn('kode', ['PA', 'KPA'])
                ->first(),
            'dataPpk' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd, 'nip' => $request->ppk])
                ->whereIn('kode', ['PPK'])
                ->first()
        ];

        return view('laporan_kontrak.pengadaan')->with($data);
    }

    public function dataKontrak($request)
    {
        $dataKontrak = DB::table('trhkontrak')
            ->where(['nomorkontrak' => $request->no_kontrak, 'idkontrak' => $request->id_kontrak, 'kodeskpd' => $request->kd_skpd])
            ->first();

        return $dataKontrak;
    }
}
