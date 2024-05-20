<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKontrakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cetak(Request $request)
    {
        $dataKontrak = DB::table('trhkontrak')
            ->where(['nomorkontrak' => $request->no_kontrak, 'idkontrak' => $request->id_kontrak, 'kodeskpd' => $request->kd_skpd])
            ->first();

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
                ->where(['kd_skpd' => $request->kd_skpd, 'nip' => $request->pptk])
                ->whereIn('kode', ['PA', 'KPA'])
                ->first()
        ];

        return view('laporan_kontrak.cetak')->with($data);
    }
}
