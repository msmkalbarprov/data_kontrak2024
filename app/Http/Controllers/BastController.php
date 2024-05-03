<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BastController extends Controller
{
    protected $connection;
    protected $tahun;

    public function __construct()
    {
        $this->connection = DB::connection('simakda');
        $this->tahun = tahun();
    }

    public function index()
    {
        return view('bast.index');
    }

    public function load(Request $request)
    {
        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('trhbast')
            ->where(function ($query) {
                $query->where('kodeskpd', Auth::user()->kd_skpd);
            });

        // Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('nomorbapbast', 'like', "%" . $search . "%");
            $query->orWhere('nomorpesanan', 'like', "%" . $search . "%");
        });

        $orderByName = 'nomorbapbast';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'nomorbapbast';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $users = $query->skip($skip)->take($pageLength)->get();


        return DataTables::of($users)
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("bast.edit", ['nomorpesanan' => Crypt::encrypt($row->nomorpesanan), 'nomorbapbast' => Crypt::encrypt($row->nomorbapbast), 'kd_skpd' => Crypt::encrypt($row->kodeskpd), 'idkontrak' => Crypt::encrypt($row->idkontrak)]) . '" class="btn btn-sm btn-warning" style="margin-right:4px"><i class="fadeIn animated bx bx-edit"></i></a>';

                $btn .= '<a onclick="hapus(\'' . $row->nomorpesanan . '\',\'' . $row->nomorbapbast . '\',\'' . $row->idkontrak . '\',\'' . $row->kodeskpd . '\')" class="btn btn-sm btn-danger"><i class="fadeIn animated bx bx-trash"></i></a>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $daftar_rekening = $this->connection
            ->table('ms_rekening_bank_online')
            ->select('rekening', 'bank', 'nm_bank', 'npwp', 'nmrekan')
            ->where(['kd_skpd' => Auth::user()->kd_skpd])
            ->get();

        $skpd = $this->connection
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->where(['kd_skpd' => Auth::user()->kd_skpd])
            ->first();

        $tahun = $this->tahun;

        $status_anggaran = status_anggaran();

        $daftar_kontrak_awal = DB::table('trhkontrak as a')
            ->selectRaw("a.*,(select isnull(sum(realisasifisik),0) from trhbast where a.idkontrak=idkontrak and a.kodeskpd=kodeskpd) as realisasi_fisik_lalu")
            ->where(['a.kodeskpd' => Auth::user()->kd_skpd, 'a.statusAdendum' => '0'])
            ->get();

        if ($status_anggaran == '0') {
            return redirect()
                ->route('bast.index')
                ->with('message', 'Anggaran belum disahkan, hubungi Anggaran!');;
        }

        return view('bast.create', compact('daftar_rekening', 'skpd', 'tahun', 'status_anggaran', 'daftar_kontrak_awal'));
    }

    public function store(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();

        try {
            $nomor = $data['jenis_kontrak'] == 2 ? $data['no_bap'] : $data['no_bast'];
            $tanggal = $data['jenis_kontrak'] == 2 ? $data['tgl_bap'] : $data['tgl_bast'];

            $cekNomorBapBast = DB::table('trhbast')
                ->where(['nomorbapbast' => $nomor, 'kodeskpd' => $data['kd_skpd']])
                ->count();

            if ($cekNomorBapBast > 0) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Nomor BAP/BAST telah digunakan!',
                ], 400);
            }

            $dataKontrak = DB::table('trhkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'nomorkontrak' => $data['no_kontrak'], 'kodeskpd' => $data['kd_skpd']])
                ->first();

            $skpd = $this->connection
                ->table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->first();

            DB::table('trhbast')
                ->insert([
                    'nomorpesanan' => $data['no_pesanan'],
                    'tanggalpesanan' => $data['tgl_pesanan'],
                    'nomorbapbast' => $nomor,
                    'tanggalbapbast' => $tanggal,
                    'kodeskpd' => $data['kd_skpd'],
                    'namaskpd' => $skpd->nm_skpd,
                    'idkontrak' => $data['id_kontrak'],
                    'nomorkontrak' => $data['no_kontrak'],
                    'statuspekerjaan' => strval($data['status_kontrak']),
                    'jenis' => strval($data['jenis_kontrak']),
                    'keterangan' => $data['keterangan'],
                    'realisasifisik' => floatval($data['realisasi_fisik']),
                    'nilai' => floatval($data['total_rincian_kontrak']),
                    'jenisspp' => $dataKontrak->jenisspp,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_username' => Auth::user()->username,
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            foreach ($data['kontrak'] as $kontrak) {
                $validasiSumberKontrak = $this->validasiSumberKontrak($kontrak, $data['anggaran_kontrak'], $data['no_kontrak'], $data['id_kontrak']);

                if ($validasiSumberKontrak != '') {
                    return response()->json([
                        'status' => false,
                        'error' => $validasiSumberKontrak,
                    ], 400);
                }

                $validasiSumberAnggaranSaatIni = $this->validasiSumberAnggaran($kontrak, $data['status_anggaran']);

                if ($validasiSumberAnggaranSaatIni != '') {
                    return response()->json([
                        'status' => false,
                        'error' => $validasiSumberAnggaranSaatIni,
                    ], 400);
                }

                $validasiRealisasi = $this->validasiRealisasi($kontrak, $data['no_kontrak'], $data['id_kontrak']);

                if ($validasiRealisasi != '') {
                    return response()->json([
                        'status' => false,
                        'error' => $validasiRealisasi,
                    ], 400);
                }
            }

            if (isset($data['kontrak'])) {
                DB::table('trdbapbast')
                    ->insert(array_map(function ($value) use ($data, $nomor) {
                        return [
                            'nomorpesanan' => $data['no_pesanan'],
                            'nomorbapbast' => $nomor,
                            'kodeskpd' => $data['kd_skpd'],
                            'kodesubkegiatan' => $value['kd_sub_kegiatan'],
                            'kodeakun' => $value['kd_rek6'],
                            'kodebarang' => $value['kd_barang'],
                            'idtrdpo' => $value['id'],
                            'nomorpo' => $value['no_po'],
                            'header' => $value['header'],
                            'subheader' => $value['sub_header'],
                            'uraianbarang' => $value['uraian'],
                            'spek' => strval($value['spesifikasi']),
                            'harga' => floatval($value['harga']),
                            'volume1' => floatval($value['input_volume1']),
                            'volume2' => floatval($value['input_volume2']),
                            'volume3' => floatval($value['input_volume3']),
                            'volume4' => floatval($value['input_volume4']),
                            'satuan1' => $value['satuan1'],
                            'satuan2' => $value['satuan2'],
                            'satuan3' => $value['satuan3'],
                            'satuan4' => $value['satuan4'],
                            'nilai' => floatval($value['total']),
                            'kodesumberdana' => $value['sumber'],
                        ];
                    }, $data['kontrak']));
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Error, Data tidak berhasil ditambahkan!',
                'e' => $e->getMessage()
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        $idkontrak = $request->idkontrak;
        $nomorpesanan = $request->nomorpesanan;
        $nomorbapbast = $request->nomorbapbast;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();

        try {
            $cekKontrakAdendum = DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd])
                ->where('adendum', '!=', '0')
                ->count();

            if ($cekKontrakAdendum > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kontrak telah diadendum, tidak bisa dihapus!'
                ], 400);
            }


            DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontrak' => $nomorkontrak])
                ->delete();

            DB::table('trdkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontrak' => $nomorkontrak])
                ->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Data tidak berhasil dihapus'
            ], 400);
        }
    }

    public function cekRincianBast(Request $request)
    {
        $data = $request->data;
        $status_anggaran = $request->status_anggaran;
        $kontrak = $request->kontrak;
        $id_kontrak = $request->id_kontrak;

        try {
            $validasiSumberKontrak = $this->validasiSumberKontrak($data, $data['jns_ang'], $kontrak, $id_kontrak);

            if ($validasiSumberKontrak != '') {
                return response()->json([
                    'status' => false,
                    'tipe' => 'Error, Validasi Sumber Kontrak',
                    'error' => $validasiSumberKontrak,
                ], 400);
            }

            $validasiSumberAnggaranSaatIni = $this->validasiSumberAnggaran($data, $status_anggaran);

            if ($validasiSumberAnggaranSaatIni != '') {
                return response()->json([
                    'status' => false,
                    'tipe' => 'Error, Validasi Sumber Anggaran Saat Ini',
                    'error' => $validasiSumberAnggaranSaatIni,
                ], 400);
            }

            $validasiRealisasi = $this->validasiRealisasi($data, $kontrak, $id_kontrak);

            if ($validasiRealisasi != '') {
                return response()->json([
                    'status' => false,
                    'tipe' => 'Error, Validasi Realisasi',
                    'error' => $validasiRealisasi,
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Data tidak berhasil ditambahkan!',
                'e' => $e->getMessage()
            ], 400);
        }
    }

    // Untuk mengecek Sumber Sesuai Anggaran
    public function validasiSumberAnggaran($data, $status_anggaran)
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
                'a.kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'a.kd_rek6' => $data['kd_rek6'],
                'a.jns_ang' => $status_anggaran,
                'b.kd_barang' => $data['kd_barang'],
                'b.header' => $data['header'],
                'b.sub_header' => $data['sub_header'],
            ])
            ->select('a.sumber', 'a.nm_sumber', 'b.volume1', 'b.volume2', 'b.volume3', 'b.volume4', 'b.satuan1', 'b.satuan2', 'b.satuan3', 'b.satuan4', 'b.harga', 'b.total', 'b.id', 'b.no_po', 'b.uraian', 'b.spesifikasi')
            ->first();

        $message = '';

        if ($data['input_volume1'] > $sumber->volume1) {
            $message .= "Input volume 1 melebihi anggaran volume 1 : " . rupiah($sumber->volume1) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        if ($data['input_volume2'] > $sumber->volume2) {
            $message .= "Input volume 2 melebihi anggaran volume 2 : " . rupiah($sumber->volume2) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        if ($data['input_volume3'] > $sumber->volume3) {
            $message .= "Input volume 3 melebihi anggaran volume 3 : " . rupiah($sumber->volume3) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        if ($data['input_volume4'] > $sumber->volume4) {
            $message .= "Input volume 4 melebihi anggaran volume 4 : " . rupiah($sumber->volume4) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        return $message;
    }

    public function validasiSumberKontrak($data, $status_anggaran, $kontrak, $id_kontrak)
    {
        $sumber = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where([
                'b.kodeskpd' => Auth::user()->kd_skpd,
                'b.nomorkontrak' => $kontrak,
                'b.idkontrak' => $id_kontrak,
                'a.kodesubkegiatan' => $data['kd_sub_kegiatan'],
                'a.kodeakun' => $data['kd_rek6'],
                'a.kodebarang' => $data['kd_barang'],
                'a.header' => $data['header'],
                'a.subheader' => $data['sub_header'],
                'a.kodesumberdana' => $data['sumber'],
            ])
            ->select('a.volume1', 'a.volume2', 'a.volume3', 'a.volume4')
            ->first();

        $message = '';

        if ($data['input_volume1'] > $sumber->volume1) {
            $message .= "Input volume 1 melebihi anggaran volume 1 : " . rupiah($sumber->volume1) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        if ($data['input_volume2'] > $sumber->volume2) {
            $message .= "Input volume 2 melebihi anggaran volume 2 : " . rupiah($sumber->volume2) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        if ($data['input_volume3'] > $sumber->volume3) {
            $message .= "Input volume 3 melebihi anggaran volume 3 : " . rupiah($sumber->volume3) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        if ($data['input_volume4'] > $sumber->volume4) {
            $message .= "Input volume 4 melebihi anggaran volume 4 : " . rupiah($sumber->volume4) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
        }

        return $message;
    }

    public function validasiRealisasi($data, $kontrak, $id_kontrak)
    {
        $dataKontrak = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where([
                'b.kodeskpd' => Auth::user()->kd_skpd,
                'b.nomorkontrak' => $kontrak,
                'b.idkontrak' => $id_kontrak,
                'a.kodesubkegiatan' => $data['kd_sub_kegiatan'],
                'a.kodeakun' => $data['kd_rek6'],
                'a.kodebarang' => $data['kd_barang'],
                'a.header' => $data['header'],
                'a.subheader' => $data['sub_header'],
                'a.kodesumberdana' => $data['sumber'],
            ])
            ->select('a.volume1', 'a.volume2', 'a.volume3', 'a.volume4')
            ->first();

        $realisasiKontrak = DB::table('trdbapbast as a')
            ->join('trhbast as b', function ($join) {
                $join->on('a.nomorpesanan', '=', 'b.nomorpesanan');
                $join->on('a.nomorbapbast', '=', 'b.nomorbapbast');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where([
                'a.kodeskpd' => Auth::user()->kd_skpd,
                'a.kodesubkegiatan' => $data['kd_sub_kegiatan'],
                'a.kodeakun' => $data['kd_rek6'],
                'a.kodebarang' => $data['kd_barang'],
                'a.header' => $data['header'],
                'a.subheader' => $data['sub_header'],
                'a.kodesumberdana' => $data['sumber'],
            ])
            ->selectRaw("ISNULL(sum(volume1),0) as volume1,ISNULL(sum(volume2),0) as volume2,ISNULL(sum(volume3),0) as volume3,ISNULL(sum(volume4),0) as volume4")
            ->first();

        $dataAnggaranSaatIni = $this->connection
            ->table('trdpo as a')
            ->join('trdpo_rinci as b', function ($join) {
                $join->on('a.jns_ang', '=', 'b.jns_ang');
                $join->on('a.no_trdrka', '=', 'b.no_trdrka');
                $join->on('a.header', '=', 'b.header');
            })
            ->where([
                'a.kd_skpd' => Auth::user()->kd_skpd,
                'a.kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'a.kd_rek6' => $data['kd_rek6'],
                'a.jns_ang' => status_anggaran(),
                'b.kd_barang' => $data['kd_barang'],
                'b.header' => $data['header'],
                'b.sub_header' => $data['sub_header'],
            ])
            ->select('b.volume1', 'b.volume2', 'b.volume3', 'b.volume4')
            ->first();

        $message = '';

        // PROTEKSI REALISASI TERHADAP NOMOR KONTRAK (AWAL)
        if (floatval($data['input_volume1']) > ($dataKontrak->volume1 - $realisasiKontrak->volume1)) {
            $message .= "Input volume 1 melebihi sisa anggaran kontrak volume 1 : " . rupiah($dataKontrak->volume1 - $realisasiKontrak->volume1) . " <br/> ";
        }

        if (floatval($data['input_volume2']) > ($dataKontrak->volume2 - $realisasiKontrak->volume2)) {
            $message .= "Input volume 2 melebihi sisa anggaran kontrak volume 2 : " . rupiah($dataKontrak->volume2 - $realisasiKontrak->volume2) . " <br/> ";
        }

        if (floatval($data['input_volume3']) > ($dataKontrak->volume3 - $realisasiKontrak->volume3)) {
            $message .= "Input volume 3 melebihi sisa anggaran kontrak volume 3 : " . rupiah($dataKontrak->volume3 - $realisasiKontrak->volume3) . " <br/> ";
        }

        if (floatval($data['input_volume4']) > ($dataKontrak->volume3 - $realisasiKontrak->volume3)) {
            $message .= "Input volume 4 melebihi sisa anggaran kontrak volume 4 : " . rupiah($dataKontrak->volume4 - $realisasiKontrak->volume4) . " <br/> ";
        }
        // PROTEKSI REALISASI TERHADAP NOMOR KONTRAK (AKHIR)

        // PROTEKSI REALISASI TERHADAP ANGGARAN SAAT INI (AKHIR)
        if (floatval($data['input_volume1']) > ($dataAnggaranSaatIni->volume1 - $realisasiKontrak->volume1)) {
            $message .= "Input volume 1 melebihi sisa anggaran volume 1 : " . rupiah($dataAnggaranSaatIni->volume1 - $realisasiKontrak->volume1) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
        }

        if (floatval($data['input_volume2']) > ($dataAnggaranSaatIni->volume2 - $realisasiKontrak->volume2)) {
            $message .= "Input volume 2 melebihi sisa anggaran volume 2 : " . rupiah($dataAnggaranSaatIni->volume2 - $realisasiKontrak->volume2) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
        }

        if (floatval($data['input_volume3']) > ($dataAnggaranSaatIni->volume3 - $realisasiKontrak->volume3)) {
            $message .= "Input volume 3 melebihi sisa anggaran volume 3 : " . rupiah($dataAnggaranSaatIni->volume3 - $realisasiKontrak->volume3) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
        }

        if (floatval($data['input_volume4']) > ($dataAnggaranSaatIni->volume3 - $realisasiKontrak->volume3)) {
            $message .= "Input volume 4 melebihi sisa anggaran volume 4 : " . rupiah($dataAnggaranSaatIni->volume4 - $realisasiKontrak->volume4) . ". Jenis Anggaran : " . namaAnggaran(status_anggaran()) . " <br/> ";
        }
        // PROTEKSI REALISASI TERHADAP ANGGARAN SAAT INI (AKHIR)

        return $message;
    }
}