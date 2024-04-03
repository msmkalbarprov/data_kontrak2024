<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function rupiah($data)
{
    return number_format($data, 2, ',', '.');
}

function tahun()
{
    return '2024';
}

function status_anggaran()
{
    $kd_skpd = Auth::user()->kd_skpd;

    $data = DB::connection('simakda')
        ->table('trhrka')
        ->select('jns_ang')
        ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
        ->orderByDesc('tgl_dpa')
        ->first();

    return isset($data) ? $data->jns_ang : '0';
}

function tipeAnggaran($request)
{
    $kd_skpd = Auth::user()->kd_skpd;

    $anggaranSah = DB::connection('simakda')
        ->table('trhrka')
        ->select('jns_ang')
        ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
        ->orderByDesc('tgl_dpa')
        ->first();

    $idAnggaran = DB::connection('simakda')
        ->table('tb_status_anggaran')
        ->where(['kode' => $anggaranSah->jns_ang])
        ->first()
        ->id;

    $idAnggaranKontrak = DB::connection('simakda')
        ->table('tb_status_anggaran')
        ->where(['kode' => $request->jns_ang])
        ->first()
        ->id;

    return $idAnggaran <= $idAnggaranKontrak ? 1 : 0;
}
