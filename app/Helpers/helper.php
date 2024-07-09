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

function namaSkpd($kd_skpd)
{
    $data = DB::connection('simakda')
        ->table('ms_skpd')
        ->where(['kd_skpd' => $kd_skpd])
        ->first()
        ->nm_skpd;

    return $data;
}

function namaProgram($kd_program)
{
    $data = DB::connection('simakda')
        ->table('ms_program')
        ->where(['kd_program' => $kd_program])
        ->first()
        ->nm_program;

    return $data;
}

function namaKegiatan($kd_kegiatan)
{
    $data = DB::connection('simakda')
        ->table('ms_kegiatan')
        ->where(['kd_kegiatan' => $kd_kegiatan])
        ->first()
        ->nm_kegiatan;

    return $data;
}

function namaSubKegiatan($kd_sub_kegiatan)
{
    $data = DB::connection('simakda')
        ->table('ms_sub_kegiatan')
        ->where(['kd_sub_kegiatan' => $kd_sub_kegiatan])
        ->first()
        ->nm_sub_kegiatan;

    return $data;
}

function namaRekening($rekening)
{
    $data = DB::connection('simakda')
        ->table('ms_rek6')
        ->where(['kd_rek6' => $rekening])
        ->first()
        ->nm_rek6;

    return $data;
}

function namaSumber($sumber)
{
    $data = DB::connection('simakda')
        ->table('sumber_dana')
        ->where('kd_sumber_dana1', $sumber)
        ->first()
        ->nm_sumber_dana1;

    return $data;
}

function depan($number)
{
    $number = abs($number);
    $nomor_depan = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $depans = "";

    if ($number < 12) {
        $depans = " " . $nomor_depan[$number];
    } else if ($number < 20) {
        $depans = depan($number - 10) . " belas";
    } else if ($number < 100) {
        $depans = depan($number / 10) . " puluh " . depan(fmod($number, 10));
    } else if ($number < 200) {
        $depans = "seratus " . depan($number - 100);
    } else if ($number < 1000) {
        $depans = depan($number / 100) . " ratus " . depan(fmod($number, 100));
        //$depans = depan($number/100)." Ratus ".depan($number%100);
    } else if ($number < 2000) {
        $depans = "seribu " . depan($number - 1000);
    } else if ($number < 1000000) {
        $depans = depan($number / 1000) . " ribu " . depan(fmod($number, 1000));
    } else if ($number < 1000000000) {
        $depans = depan($number / 1000000) . " juta " . depan(fmod($number, 1000000));
    } else if ($number < 1000000000000) {
        $depans = depan($number / 1000000000) . " milyar " . depan(fmod($number, 1000000000));
        //$depans = ($number/1000000000)." Milyar ".(fmod($number,1000000000))."------".$number;

    } else if ($number < 1000000000000000) {
        $depans = depan($number / 1000000000000) . " triliun " . depan(fmod($number, 1000000000000));
        //$depans = ($number/1000000000)." Milyar ".(fmod($number,1000000000))."------".$number;

    } else {
        $depans = "Undefined";
    }
    return $depans;
}

function paguAnggaran($item)
{
    $data = DB::connection('simakda')
        ->table('trdrka')
        ->where(['jns_ang' => $item->jns_ang, 'kd_skpd' => $item->kodeskpd, 'kd_sub_kegiatan' => $item->kodesubkegiatan, 'kd_rek6' => $item->kodeakun])
        ->first();

    return $data->nilai;
}

function left($string, $count)
{
    return substr($string, 0, $count);
}

function right($value, $count)
{
    return substr($value, ($count * -1));
}

function dotrek($rek)
{
    $nrek = strlen($rek);
    switch ($nrek) {
        case 1:
            $rek = left($rek, 1);
            break;
        case 2:
            $rek = left($rek, 1) . '.' . substr($rek, 1, 1);
            break;
        case 4:
            $rek = left($rek, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2);
            break;
        case 6:
            $rek = left($rek, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2);
            break;
        case 8:
            $rek = left($rek, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2) . '.' . substr($rek, 6, 2);
            break;
        case 12:
            $rek = left($rek, 1) . '.' . substr($rek, 1, 1) . '.' . substr($rek, 2, 2) . '.' . substr($rek, 4, 2) . '.' . substr($rek, 6, 2) . '.' . substr($rek, 8, 4);
            break;
        default:
            $rek = "";
    }
    return $rek;
}

function getTingkatanRekening($rekening)
{
    if (strlen($rekening) == 12) return '6';
    if (strlen($rekening) == 8) return '5';
    if (strlen($rekening) == 6) return '4';
    if (strlen($rekening) == 4) return '3';
    if (strlen($rekening) == 2) return '2';
    if (strlen($rekening) == 1) return '1';
}

function setRekeningAkun($rekening)
{
    $connection = DB::connection('simakda');
    $rek = getTingkatanRekening($rekening);

    $rekening = $connection->table("simakda_2024.dbo.ms_rek$rek as rek")
        ->select("rek.nm_rek$rek as nama")
        ->where(["rek.kd_rek$rek" => $rekening])
        ->first();

    return $rekening->nama;
}

function cekDetailKontrak($request)
{
    $message = '';

    foreach ($request as $item) {
        if (!empty($item['detail'])) {
            if ($item['detail']['kelompok'] == '5201') {
                if (!$item['detail']['nomor_sertifikat']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi nomor sertifikat! <br/><br/>";
                }

                if (!$item['detail']['tanggal_sertifikat']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi tanggal sertifikat! <br/><br/>";
                }

                if (!$item['detail']['panjang']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi panjang! <br/><br/>";
                }

                if (!$item['detail']['lebar']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi lebar! <br/><br/>";
                }

                if (!$item['detail']['luas']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi luas! <br/><br/>";
                }

                if ($item['detail']['panjang'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Panjang tidak boleh 0! <br/><br/>";
                }

                if ($item['detail']['lebar'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Lebar tidak boleh 0! <br/><br/>";
                }

                if ($item['detail']['luas'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Luas tidak boleh 0! <br/><br/>";
                }

                if (!$item['detail']['status_tanah']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih status tanah! <br/><br/>";
                }

                if (!$item['detail']['penggunaan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Penggunaan tidak boleh kosong! <br/><br/>";
                }

                if ($message != '') {
                    return $message;
                }
            }

            if ($item['detail']['kelompok'] == '5202') {
                if (!$item['detail']['merk']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Merk tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['ukuran']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Ukuran tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['pabrik']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Pabrik tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['rangka']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Rangka tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['mesin']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Mesin tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['polisi']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Polisi tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['bpkb']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .BPKB tidak boleh kosong! <br/><br/>";
                }

                if (!$item['detail']['bahan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Bahan tidak boleh kosong! <br/>";
                }

                if ($message != '') {
                    return $message;
                }
            }

            if ($item['detail']['kelompok'] == '5203') {
                if (!$item['detail']['bertingkat'] && !$item['detail']['beton']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih kontruksi bangunan! <br/><br/>";
                }

                if (!$item['detail']['panjang']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi panjang! <br/><br/>";
                }

                if (!$item['detail']['lebar']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi lebar! <br/><br/>";
                }

                if (!$item['detail']['luas']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi luas! <br/><br/>";
                }

                if ($item['detail']['panjang'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Panjang tidak boleh 0! <br/><br/>";
                }

                if ($item['detail']['lebar'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Lebar tidak boleh 0! <br/><br/>";
                }

                if ($item['detail']['luas'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Luas tidak boleh 0! <br/><br/>";
                }

                if (!$item['detail']['status_tanah']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih status tanah! <br/><br/>";
                }

                if (!$item['detail']['penggunaan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Penggunaan tidak boleh kosong! <br/><br/>";
                }

                if ($message != '') {
                    return $message;
                }
            }

            if ($item['detail']['kelompok'] == '5204') {
                if (!$item['detail']['panjang']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi panjang! <br/><br/>";
                }

                if (!$item['detail']['lebar']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi lebar! <br/><br/>";
                }

                if (!$item['detail']['luas']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi luas! <br/><br/>";
                }

                if ($item['detail']['panjang'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Panjang tidak boleh 0! <br/><br/>";
                }

                if ($item['detail']['lebar'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Lebar tidak boleh 0! <br/><br/>";
                }

                if ($item['detail']['luas'] == 0) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Luas tidak boleh 0! <br/><br/>";
                }

                if (!$item['detail']['status_tanah']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih status tanah! <br/><br/>";
                }

                if (!$item['detail']['penggunaan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Penggunaan tidak boleh kosong! <br/><br/>";
                }

                if ($message != '') {
                    return $message;
                }
            }

            if ($item['detail']['kelompok'] == '5205') {
                if (!$item['detail']['judul_buku']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi judul buku/perpustakaan! <br/><br/>";
                }

                if (!$item['detail']['pencipta_buku']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi pencipta buku/perpustakaan! <br/><br/>";
                }

                if (!$item['detail']['spesifikasi_buku']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi spesifikasi buku/perpustakaan! <br/><br/>";
                }

                if (!$item['detail']['asal_daerah']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi asal daerah barang bercorak! <br/><br/>";
                }

                if (!$item['detail']['pencipta_daerah']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi pencipta barang bercorak! <br/><br/>";
                }

                if (!$item['detail']['bahan_daerah']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi bahan barang bercorak! <br/><br/>";
                }

                if (!$item['detail']['jenis_hewan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi jenis hewan/ternak tumbuhan! <br/><br/>";
                }

                if (!$item['detail']['ukuran_hewan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi ukuran hewan/ternak tumbuhan! <br/><br/>";
                }

                if (!$item['detail']['nik_hewan']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi NIK! <br/><br/>";
                }


                if ($message != '') {
                    return $message;
                }
            }

            if ($item['detail']['kelompok'] == '5206') {
                if (!$item['detail']['nama_aplikasi']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi nama aplikasi! <br/><br/>";
                }

                if (!$item['detail']['judul_aplikasi']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi judul aplikasi! <br/><br/>";
                }

                if (!$item['detail']['pencipta_aplikasi']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi pencipta aplikasi! <br/><br/>";
                }

                if (!$item['detail']['spesifikasi_aplikasi']) {
                    $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi spesifikasi aplikasi! <br/><br/>";
                }


                if ($message != '') {
                    return $message;
                }
            }
        }
    }

    return $message;
}

function dataDetailKontrak($request)
{
    if (!empty($request)) {
        if ($request['kelompok'] == '5201') {
            $detailKontrak = [
                'kelompok' => $request['kelompok'],
                'nomor_sertifikat' => $request['nomor_sertifikat'],
                'tanggal_sertifikat' => $request['tanggal_sertifikat'],
                'status_tanah' => $request['status_tanah'],
                'penggunaan' => $request['penggunaan'],
                'panjang' => $request['panjang'],
                'lebar' => $request['lebar'],
                'luas' => $request['luas'],
            ];
        } else if ($request['kelompok'] == '5202') {
            $detailKontrak = [
                'kelompok' => $request['kelompok'],
                'merk' => $request['merk'],
                'ukuran' => $request['ukuran'],
                'pabrik' => $request['pabrik'],
                'rangka' => $request['rangka'],
                'mesin' => $request['mesin'],
                'polisi' => $request['polisi'],
                'bpkb' => $request['bpkb'],
                'bahan' => $request['bahan'],
            ];
        } else if ($request['kelompok'] == '5203') {
            $detailKontrak = [
                'kelompok' => $request['kelompok'],
                'status_tanah' => $request['status_tanah'],
                'penggunaan' => $request['penggunaan'],
                'panjang' => $request['panjang'],
                'lebar' => $request['lebar'],
                'luas' => $request['luas'],
                'bertingkat' => $request['bertingkat'],
                'beton' => $request['beton'],
            ];
        } else if ($request['kelompok'] == '5204') {
            $detailKontrak = [
                'kelompok' => $request['kelompok'],
                'status_tanah' => $request['status_tanah'],
                'penggunaan' => $request['penggunaan'],
                'panjang' => $request['panjang'],
                'lebar' => $request['lebar'],
                'luas' => $request['luas'],
            ];
        } else if ($request['kelompok'] == '5205') {
            $detailKontrak = [
                'kelompok' => $request['kelompok'],
                'judul_buku' => $request['judul_buku'],
                'pencipta_buku' => $request['pencipta_buku'],
                'spesifikasi_buku' => $request['spesifikasi_buku'],
                'asal_daerah' => $request['asal_daerah'],
                'pencipta_daerah' => $request['pencipta_daerah'],
                'bahan_daerah' => $request['bahan_daerah'],
                'jenis_hewan' => $request['jenis_hewan'],
                'ukuran_hewan' => $request['ukuran_hewan'],
                'nik_hewan' => $request['nik_hewan'],
            ];
        } else if ($request['kelompok'] == '5206') {
            $detailKontrak = [
                'kelompok' => $request['kelompok'],
                'nama_aplikasi' => $request['nama_aplikasi'],
                'judul_aplikasi' => $request['judul_aplikasi'],
                'pencipta_aplikasi' => $request['pencipta_aplikasi'],
                'spesifikasi_aplikasi' => $request['spesifikasi_aplikasi'],
            ];
        } else {
            $detailKontrak = [];
        }
    } else {
        $detailKontrak = [];
    }

    return $detailKontrak;
}
