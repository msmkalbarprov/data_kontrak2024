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

function namaAnggaran($request)
{
    $data = DB::connection('simakda')
        ->table('tb_status_anggaran')
        ->select('nama')
        ->where(['kode' => $request])
        ->first();

    return $data->nama;
}

function filter_menu()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('users as a')
        ->join('model_has_roles as b', 'a.id', '=', 'b.model_id')
        ->join('roles as c', 'b.role_id', '=', 'c.uuid')
        ->join('role_has_permissions as d', 'c.uuid', '=', 'd.role_id')
        ->join('permissions as e', 'd.permission_id', '=', 'e.uuid')
        ->select('e.*')
        ->where(['a.id' => $id, 'e.parent' => ''])
        ->orderBy('e.uuid')
        ->get();

    return $hak_akses;
}

function sub_menu()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('users as a')
        ->join('model_has_roles as b', 'a.id', '=', 'b.model_id')
        ->join('roles as c', 'b.role_id', '=', 'c.uuid')
        ->join('role_has_permissions as d', 'c.uuid', '=', 'd.role_id')
        ->join('permissions as e', 'd.permission_id', '=', 'e.uuid')
        ->select('e.*')
        ->where(['a.id' => $id])
        ->where('e.parent', '!=', '')
        ->orderBy('e.uuid')
        ->get();

    return $hak_akses;
}
